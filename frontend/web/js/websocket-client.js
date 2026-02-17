/**
 * Meeting Room Booking - WebSocket Client
 * 
 * Handles real-time notifications and updates
 * 
 * Usage:
 *   const ws = new MeetingRoomWS('ws://localhost:8080', 'auth_token');
 *   ws.connect();
 *   ws.on('booking_update', (data) => { ... });
 *   ws.subscribe('room:1');
 */

class MeetingRoomWS {
    constructor(url, authToken = null) {
        this.url = url;
        this.authToken = authToken;
        this.socket = null;
        this.connected = false;
        this.authenticated = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 3000;
        this.pingInterval = null;
        this.eventHandlers = {};
        this.subscribedChannels = [];
        this.messageQueue = [];
    }

    /**
     * Connect to WebSocket server
     */
    connect() {
        if (this.socket && (this.socket.readyState === WebSocket.CONNECTING || this.socket.readyState === WebSocket.OPEN)) {
            console.log('WebSocket already connected or connecting');
            return;
        }

        try {
            this.socket = new WebSocket(this.url);
            this.setupEventListeners();
        } catch (error) {
            console.error('WebSocket connection error:', error);
            this.scheduleReconnect();
        }
    }

    /**
     * Setup WebSocket event listeners
     */
    setupEventListeners() {
        this.socket.onopen = () => {
            console.log('WebSocket connected');
            this.connected = true;
            this.reconnectAttempts = 0;
            
            // Authenticate if token provided
            if (this.authToken) {
                this.authenticate(this.authToken);
            }
            
            // Start ping interval
            this.startPing();
            
            // Process queued messages
            this.processQueue();
            
            // Trigger connect event
            this.trigger('connect');
        };

        this.socket.onclose = (event) => {
            console.log('WebSocket disconnected:', event.code, event.reason);
            this.connected = false;
            this.authenticated = false;
            this.stopPing();
            
            this.trigger('disconnect', { code: event.code, reason: event.reason });
            
            // Attempt reconnection
            if (!event.wasClean) {
                this.scheduleReconnect();
            }
        };

        this.socket.onerror = (error) => {
            console.error('WebSocket error:', error);
            this.trigger('error', error);
        };

        this.socket.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                this.handleMessage(data);
            } catch (error) {
                console.error('Error parsing message:', error);
            }
        };
    }

    /**
     * Handle incoming message
     */
    handleMessage(data) {
        const type = data.type || 'unknown';
        
        switch (type) {
            case 'welcome':
                console.log('Server welcome:', data.message);
                this.trigger('welcome', data);
                break;
                
            case 'auth_success':
                this.authenticated = true;
                this.userId = data.userId;
                console.log('Authenticated as user:', data.userId);
                this.trigger('authenticated', data);
                
                // Resubscribe to channels
                this.resubscribe();
                break;
                
            case 'subscribed':
                console.log('Subscribed to:', data.channel);
                if (!this.subscribedChannels.includes(data.channel)) {
                    this.subscribedChannels.push(data.channel);
                }
                this.trigger('subscribed', data);
                break;
                
            case 'unsubscribed':
                console.log('Unsubscribed from:', data.channel);
                this.subscribedChannels = this.subscribedChannels.filter(c => c !== data.channel);
                this.trigger('unsubscribed', data);
                break;
                
            case 'pong':
                // Ping response received
                break;
                
            case 'error':
                console.error('Server error:', data.message);
                this.trigger('error', data);
                break;
                
            case 'notification':
                this.showNotification(data);
                this.trigger('notification', data);
                break;
                
            case 'booking_created':
            case 'booking_updated':
            case 'booking_cancelled':
            case 'booking_approved':
            case 'booking_rejected':
                this.trigger('booking_update', data);
                this.trigger(type, data);
                break;
                
            case 'announcement':
                this.showAnnouncement(data);
                this.trigger('announcement', data);
                break;
                
            default:
                // Trigger generic message event
                this.trigger('message', data);
                this.trigger(type, data);
        }
    }

    /**
     * Authenticate with server
     */
    authenticate(token) {
        this.authToken = token;
        this.send({
            type: 'auth',
            token: token
        });
    }

    /**
     * Subscribe to channel
     */
    subscribe(channel) {
        if (!this.subscribedChannels.includes(channel)) {
            this.subscribedChannels.push(channel);
        }
        
        if (this.authenticated) {
            this.send({
                type: 'subscribe',
                channel: channel
            });
        }
    }

    /**
     * Unsubscribe from channel
     */
    unsubscribe(channel) {
        this.subscribedChannels = this.subscribedChannels.filter(c => c !== channel);
        
        if (this.connected) {
            this.send({
                type: 'unsubscribe',
                channel: channel
            });
        }
    }

    /**
     * Resubscribe to all channels after reconnection
     */
    resubscribe() {
        const channels = [...this.subscribedChannels];
        this.subscribedChannels = [];
        
        channels.forEach(channel => {
            if (!channel.startsWith('user:')) {
                this.subscribe(channel);
            }
        });
    }

    /**
     * Send message to server
     */
    send(data) {
        if (this.connected && this.socket.readyState === WebSocket.OPEN) {
            this.socket.send(JSON.stringify(data));
        } else {
            // Queue message for later
            this.messageQueue.push(data);
        }
    }

    /**
     * Process queued messages
     */
    processQueue() {
        while (this.messageQueue.length > 0 && this.connected) {
            const message = this.messageQueue.shift();
            this.send(message);
        }
    }

    /**
     * Start ping interval
     */
    startPing() {
        this.stopPing();
        this.pingInterval = setInterval(() => {
            if (this.connected) {
                this.send({ type: 'ping' });
            }
        }, 30000);
    }

    /**
     * Stop ping interval
     */
    stopPing() {
        if (this.pingInterval) {
            clearInterval(this.pingInterval);
            this.pingInterval = null;
        }
    }

    /**
     * Schedule reconnection attempt
     */
    scheduleReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.log('Max reconnection attempts reached');
            this.trigger('reconnect_failed');
            return;
        }
        
        this.reconnectAttempts++;
        const delay = this.reconnectDelay * this.reconnectAttempts;
        
        console.log(`Reconnecting in ${delay}ms (attempt ${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
        
        setTimeout(() => {
            this.trigger('reconnecting', { attempt: this.reconnectAttempts });
            this.connect();
        }, delay);
    }

    /**
     * Disconnect from server
     */
    disconnect() {
        this.stopPing();
        if (this.socket) {
            this.socket.close(1000, 'Client disconnect');
        }
    }

    /**
     * Register event handler
     */
    on(event, callback) {
        if (!this.eventHandlers[event]) {
            this.eventHandlers[event] = [];
        }
        this.eventHandlers[event].push(callback);
    }

    /**
     * Remove event handler
     */
    off(event, callback) {
        if (this.eventHandlers[event]) {
            this.eventHandlers[event] = this.eventHandlers[event].filter(cb => cb !== callback);
        }
    }

    /**
     * Trigger event
     */
    trigger(event, data = {}) {
        if (this.eventHandlers[event]) {
            this.eventHandlers[event].forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error('Event handler error:', error);
                }
            });
        }
    }

    /**
     * Show browser notification
     */
    showNotification(data) {
        if (!('Notification' in window)) {
            return;
        }
        
        if (Notification.permission === 'granted') {
            this.createNotification(data);
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    this.createNotification(data);
                }
            });
        }
    }

    /**
     * Create browser notification
     */
    createNotification(data) {
        const notification = new Notification(data.title || 'การแจ้งเตือน', {
            body: data.message || data.body,
            icon: data.icon || '/images/logo-icon.png',
            tag: data.tag || 'meeting-room-notification',
            requireInteraction: data.requireInteraction || false
        });
        
        notification.onclick = () => {
            window.focus();
            if (data.url) {
                window.location.href = data.url;
            }
            notification.close();
        };
        
        // Auto close after 5 seconds
        if (!data.requireInteraction) {
            setTimeout(() => notification.close(), 5000);
        }
    }

    /**
     * Show announcement toast
     */
    showAnnouncement(data) {
        // Check if Bootstrap Toast is available
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const container = document.getElementById('toast-container') || this.createToastContainer();
            
            const toastId = 'toast-' + Date.now();
            const toastHtml = `
                <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-primary text-white">
                        <i class="bi bi-megaphone me-2"></i>
                        <strong class="me-auto">ประกาศ</strong>
                        <small>${this.formatTime(data.timestamp)}</small>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        ${data.message}
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', toastHtml);
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 10000 });
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }
    }

    /**
     * Create toast container
     */
    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1100';
        document.body.appendChild(container);
        return container;
    }

    /**
     * Format timestamp
     */
    formatTime(timestamp) {
        const date = new Date(timestamp * 1000);
        return date.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' });
    }

    /**
     * Get connection status
     */
    getStatus() {
        return {
            connected: this.connected,
            authenticated: this.authenticated,
            userId: this.userId,
            channels: this.subscribedChannels,
            queuedMessages: this.messageQueue.length
        };
    }
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MeetingRoomWS;
}

// Global instance
if (typeof window !== 'undefined') {
    window.MeetingRoomWS = MeetingRoomWS;
}
