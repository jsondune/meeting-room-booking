<?php

namespace console\components;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use common\models\User;
use yii\helpers\Json;

/**
 * WebSocket Server for Real-time Notifications
 * Handles booking updates, approvals, and system notifications
 */
class WebSocketServer implements MessageComponentInterface
{
    /**
     * @var \SplObjectStorage Connected clients storage
     */
    protected $clients;

    /**
     * @var array Map of user IDs to their connections
     */
    protected $userConnections = [];

    /**
     * @var array Map of connection resource IDs to user IDs
     */
    protected $connectionUsers = [];

    /**
     * @var array Subscription channels
     */
    protected $channels = [];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        echo "WebSocket Server initialized\n";
    }

    /**
     * Called when a new client connects
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $connId = $conn->resourceId;
        
        echo "New connection: {$connId}\n";
        
        // Send welcome message
        $conn->send(Json::encode([
            'type' => 'welcome',
            'message' => 'Connected to Meeting Room Booking WebSocket Server',
            'connectionId' => $connId,
            'timestamp' => time(),
        ]));
    }

    /**
     * Called when a message is received from a client
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $connId = $from->resourceId;
        
        try {
            $data = Json::decode($msg);
            $action = $data['action'] ?? null;
            
            echo "Message from {$connId}: {$action}\n";
            
            switch ($action) {
                case 'authenticate':
                    $this->handleAuthenticate($from, $data);
                    break;
                    
                case 'subscribe':
                    $this->handleSubscribe($from, $data);
                    break;
                    
                case 'unsubscribe':
                    $this->handleUnsubscribe($from, $data);
                    break;
                    
                case 'ping':
                    $from->send(Json::encode([
                        'type' => 'pong',
                        'timestamp' => time(),
                    ]));
                    break;
                    
                default:
                    $from->send(Json::encode([
                        'type' => 'error',
                        'message' => 'Unknown action: ' . $action,
                    ]));
            }
        } catch (\Exception $e) {
            echo "Error processing message: {$e->getMessage()}\n";
            $from->send(Json::encode([
                'type' => 'error',
                'message' => 'Invalid message format',
            ]));
        }
    }

    /**
     * Called when a client disconnects
     */
    public function onClose(ConnectionInterface $conn)
    {
        $connId = $conn->resourceId;
        
        // Remove from user connections
        if (isset($this->connectionUsers[$connId])) {
            $userId = $this->connectionUsers[$connId];
            if (isset($this->userConnections[$userId])) {
                unset($this->userConnections[$userId][$connId]);
                if (empty($this->userConnections[$userId])) {
                    unset($this->userConnections[$userId]);
                }
            }
            unset($this->connectionUsers[$connId]);
        }
        
        // Remove from channels
        foreach ($this->channels as $channel => $connections) {
            unset($this->channels[$channel][$connId]);
        }
        
        $this->clients->detach($conn);
        
        echo "Connection closed: {$connId}\n";
    }

    /**
     * Called when an error occurs
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * Handle user authentication
     */
    protected function handleAuthenticate(ConnectionInterface $conn, array $data)
    {
        $token = $data['token'] ?? null;
        $connId = $conn->resourceId;
        
        if (!$token) {
            $conn->send(Json::encode([
                'type' => 'auth_error',
                'message' => 'Token required',
            ]));
            return;
        }
        
        // Validate token (API auth key or JWT)
        $user = User::findIdentityByAccessToken($token);
        
        if (!$user) {
            $conn->send(Json::encode([
                'type' => 'auth_error',
                'message' => 'Invalid token',
            ]));
            return;
        }
        
        $userId = $user->id;
        
        // Store connection-user mapping
        if (!isset($this->userConnections[$userId])) {
            $this->userConnections[$userId] = [];
        }
        $this->userConnections[$userId][$connId] = $conn;
        $this->connectionUsers[$connId] = $userId;
        
        // Auto-subscribe to personal channel
        $personalChannel = "user:{$userId}";
        $this->channels[$personalChannel][$connId] = $conn;
        
        // Subscribe admins to admin channel
        if ($user->role === User::ROLE_ADMIN || $user->role === User::ROLE_SUPERADMIN) {
            $this->channels['admin'][$connId] = $conn;
        }
        
        // Subscribe approvers to approvals channel
        if ($user->role === User::ROLE_APPROVER) {
            $this->channels['approvals'][$connId] = $conn;
        }
        
        $conn->send(Json::encode([
            'type' => 'authenticated',
            'userId' => $userId,
            'username' => $user->username,
            'role' => $user->role,
            'channels' => array_keys(array_filter($this->channels, function($conns) use ($connId) {
                return isset($conns[$connId]);
            })),
        ]));
        
        echo "User authenticated: {$userId} ({$user->username})\n";
    }

    /**
     * Handle channel subscription
     */
    protected function handleSubscribe(ConnectionInterface $conn, array $data)
    {
        $channel = $data['channel'] ?? null;
        $connId = $conn->resourceId;
        
        if (!$channel) {
            $conn->send(Json::encode([
                'type' => 'error',
                'message' => 'Channel required',
            ]));
            return;
        }
        
        // Check authorization for protected channels
        $userId = $this->connectionUsers[$connId] ?? null;
        
        if ($this->isProtectedChannel($channel) && !$this->canSubscribe($userId, $channel)) {
            $conn->send(Json::encode([
                'type' => 'error',
                'message' => 'Not authorized to subscribe to this channel',
            ]));
            return;
        }
        
        // Subscribe to channel
        if (!isset($this->channels[$channel])) {
            $this->channels[$channel] = [];
        }
        $this->channels[$channel][$connId] = $conn;
        
        $conn->send(Json::encode([
            'type' => 'subscribed',
            'channel' => $channel,
        ]));
        
        echo "Connection {$connId} subscribed to: {$channel}\n";
    }

    /**
     * Handle channel unsubscription
     */
    protected function handleUnsubscribe(ConnectionInterface $conn, array $data)
    {
        $channel = $data['channel'] ?? null;
        $connId = $conn->resourceId;
        
        if ($channel && isset($this->channels[$channel][$connId])) {
            unset($this->channels[$channel][$connId]);
            
            $conn->send(Json::encode([
                'type' => 'unsubscribed',
                'channel' => $channel,
            ]));
        }
    }

    /**
     * Check if channel is protected
     */
    protected function isProtectedChannel(string $channel): bool
    {
        // Admin and approval channels are protected
        if (in_array($channel, ['admin', 'approvals'])) {
            return true;
        }
        
        // User personal channels are protected
        if (strpos($channel, 'user:') === 0) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if user can subscribe to channel
     */
    protected function canSubscribe(?int $userId, string $channel): bool
    {
        if (!$userId) {
            return false;
        }
        
        $user = User::findOne($userId);
        if (!$user) {
            return false;
        }
        
        // User can only subscribe to their own channel
        if (strpos($channel, 'user:') === 0) {
            $channelUserId = (int) substr($channel, 5);
            return $channelUserId === $userId;
        }
        
        // Only admins can subscribe to admin channel
        if ($channel === 'admin') {
            return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN]);
        }
        
        // Only approvers and admins can subscribe to approvals channel
        if ($channel === 'approvals') {
            return in_array($user->role, [User::ROLE_APPROVER, User::ROLE_ADMIN, User::ROLE_SUPERADMIN]);
        }
        
        return true;
    }

    /**
     * Send notification to a specific user
     */
    public function sendToUser(int $userId, array $data)
    {
        $channel = "user:{$userId}";
        $this->sendToChannel($channel, $data);
    }

    /**
     * Send notification to a channel
     */
    public function sendToChannel(string $channel, array $data)
    {
        if (!isset($this->channels[$channel])) {
            return;
        }
        
        $message = Json::encode(array_merge($data, [
            'channel' => $channel,
            'timestamp' => time(),
        ]));
        
        foreach ($this->channels[$channel] as $conn) {
            $conn->send($message);
        }
        
        echo "Sent to channel {$channel}: " . count($this->channels[$channel]) . " recipients\n";
    }

    /**
     * Broadcast to all connected clients
     */
    public function broadcast(array $data)
    {
        $message = Json::encode(array_merge($data, [
            'channel' => 'broadcast',
            'timestamp' => time(),
        ]));
        
        foreach ($this->clients as $client) {
            $client->send($message);
        }
        
        echo "Broadcast to " . count($this->clients) . " clients\n";
    }

    /**
     * Get connection count
     */
    public function getConnectionCount(): int
    {
        return count($this->clients);
    }

    /**
     * Get online users count
     */
    public function getOnlineUsersCount(): int
    {
        return count($this->userConnections);
    }
}
