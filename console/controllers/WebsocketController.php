<?php

namespace console\controllers;

use yii\console\Controller;
use yii\console\ExitCode;
use console\components\WebSocketServer;
use console\components\NotificationBridge;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as ReactServer;

/**
 * WebSocket Controller
 * Manages the WebSocket server for real-time notifications
 */
class WebsocketController extends Controller
{
    /**
     * @var string WebSocket host
     */
    public $host = '0.0.0.0';

    /**
     * @var int WebSocket port
     */
    public $port = 8080;

    /**
     * @var bool Enable debug mode
     */
    public $debug = false;

    /**
     * {@inheritdoc}
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'host',
            'port',
            'debug',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function optionAliases()
    {
        return array_merge(parent::optionAliases(), [
            'h' => 'host',
            'p' => 'port',
            'd' => 'debug',
        ]);
    }

    /**
     * Start the WebSocket server
     * 
     * Usage:
     *   yii websocket/start
     *   yii websocket/start --port=8080
     *   yii websocket/start --host=127.0.0.1 --port=9000
     *   yii websocket/start --debug
     *
     * @return int Exit code
     */
    public function actionStart()
    {
        $this->stdout("Starting WebSocket server...\n");
        $this->stdout("Host: {$this->host}\n");
        $this->stdout("Port: {$this->port}\n");
        $this->stdout("Debug: " . ($this->debug ? 'enabled' : 'disabled') . "\n");
        $this->stdout("\n");

        try {
            // Create the WebSocket server component
            $wsServer = new WebSocketServer();

            // Create the notification bridge for internal messaging
            $bridge = new NotificationBridge($wsServer);
            \Yii::$app->set('wsBridge', $bridge);

            // Create the event loop
            $loop = LoopFactory::create();

            // Create React socket server
            $socket = new ReactServer("{$this->host}:{$this->port}", $loop);

            // Create the Ratchet server stack
            $server = new IoServer(
                new HttpServer(
                    new WsServer($wsServer)
                ),
                $socket,
                $loop
            );

            // Add periodic timer to check for notifications from Redis/DB
            $loop->addPeriodicTimer(1, function() use ($bridge) {
                $bridge->processQueue();
            });

            // Add periodic timer to log stats
            if ($this->debug) {
                $loop->addPeriodicTimer(30, function() use ($wsServer) {
                    $this->stdout(sprintf(
                        "[%s] Connections: %d, Online users: %d\n",
                        date('Y-m-d H:i:s'),
                        $wsServer->getConnectionCount(),
                        $wsServer->getOnlineUsersCount()
                    ));
                });
            }

            $this->stdout("WebSocket server started on ws://{$this->host}:{$this->port}\n");
            $this->stdout("Press Ctrl+C to stop\n\n");

            // Run the server
            $server->run();

        } catch (\Exception $e) {
            $this->stderr("Error: {$e->getMessage()}\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }

    /**
     * Send a test notification
     *
     * Usage:
     *   yii websocket/test-notification --userId=1 --message="Hello World"
     *
     * @param int $userId User ID to send notification to
     * @param string $message Message to send
     * @return int Exit code
     */
    public function actionTestNotification($userId, $message = 'Test notification')
    {
        $this->stdout("Sending test notification to user {$userId}...\n");

        try {
            $bridge = new NotificationBridge();
            $bridge->queueNotification($userId, [
                'type' => 'notification',
                'title' => 'Test Notification',
                'message' => $message,
                'icon' => 'bell',
            ]);

            $this->stdout("Notification queued successfully.\n");
            return ExitCode::OK;

        } catch (\Exception $e) {
            $this->stderr("Error: {$e->getMessage()}\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Send a broadcast notification to all connected clients
     *
     * Usage:
     *   yii websocket/broadcast --message="System maintenance in 10 minutes"
     *
     * @param string $message Message to broadcast
     * @return int Exit code
     */
    public function actionBroadcast($message)
    {
        $this->stdout("Broadcasting message to all clients...\n");

        try {
            $bridge = new NotificationBridge();
            $bridge->queueBroadcast([
                'type' => 'system',
                'title' => 'System Announcement',
                'message' => $message,
                'icon' => 'megaphone',
            ]);

            $this->stdout("Broadcast queued successfully.\n");
            return ExitCode::OK;

        } catch (\Exception $e) {
            $this->stderr("Error: {$e->getMessage()}\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
