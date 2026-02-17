/**
 * Meeting Room Booking - WebSocket Client
 * Handles real-time notifications and updates
 */

class MRBWebSocket {
    constructor(options = {}) {
        this.url = options.url || this.getWebSocketUrl();
        this.token = options.token || null;
        this.autoReconnect = options.autoReconnect !== false;
        this.reconnectInterval = options.reconnectInterval || 5000;
        this.maxReconnectAttempts = options.maxReconnectAttempts || 10;
        this.debug = options.debug || false;

        this.ws = null;
        this.reconnectAttempts = 0;
        this.isConnected = false;
        this.isAuthenticated = false;
        this.connectionId = null;
        this.userId = null;
        this.channels = [];

        // Event handlers
        this.handlers = {
            open: [],
            close: [],
            error: [],
            message: [],
            authenticated: [],
            notification: [],
        };

        // Message type handlers
        this.messageHandlers = {};
    }

    /**
     * Get WebSocket URL based on current location
     */
    getWebSocketUrl() {
        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const host = window.location.hostname;
        const port = 8080; // Default WebSocket port
        return `${protocol}//${host}:${port}`;
    }

    /**
     * Connect to WebSocket server
     */
    connect() {
        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
            this.log('Already connected');
            return;
        }

        this.log(`Connecting to ${this.url}...`);

        try {
            this.ws = new WebSocket(this.url);

            this.ws.onopen = (event) => this.handleOpen(event);
            this.ws.onclose = (event) => this.handleClose(event);
            this.ws.onerror = (event) => this.handleError(event);
            this.ws.onmessage = (event) => this.handleMessage(event);
        } catch (error) {
            this.log('Connection error:', error);
            this.scheduleReconnect();
        }
    }

    /**
     * Disconnect from WebSocket server
     */
    disconnect() {
        this.autoReconnect = false;
        if (this.ws) {
            this.ws.close();
        }
    }

    /**
     * Handle connection open
     */
    handleOpen(event) {
        this.log('Connected');
        this.isConnected = true;
        this.reconnectAttempts = 0;

        // Emit open event
        this.emit('open', event);

        // Auto-authenticate if token is available
        if (this.token) {
            this.authenticate(this.token);
        }
    }

    /**
     * Handle connection close
     */
    handleClose(event) {
        this.log('Disconnected', event.code, event.reason);
        this.isConnected = false;
        this.isAuthenticated = false;

        // Emit close event
        this.emit('close', event);

        // Schedule reconnect
        if (this.autoReconnect) {
            this.scheduleReconnect();
        }
    }

    /**
     * Handle connection error
     */
    handleError(event) {
        this.log('Error:', event);
        this.emit('error', event);
    }

    /**
     * Handle incoming message
     */
    handleMessage(event) {
        try {
            const data = JSON.parse(event.data);
            this.log('Received:', data);

            // Emit general message event
            this.emit('message', data);

            // Handle specific message types
            const type = data.type;
            if (type && this.messageHandlers[type]) {
                this.messageHandlers[type].forEach(handler => handler(data));
            }

            // Handle system messages
            switch (type) {
                case 'welcome':
                    this.connectionId = data.connectionId;
                    break;

                case 'authenticated':
                    this.isAuthenticated = true;
                    this.userId = data.userId;
                    this.channels = data.channels || [];
                    this.emit('authenticated', data);
                    break;

                case 'auth_error':
                    this.log('Authentication failed:', data.message);
                    break;

                case 'subscribed':
                    if (!this.channels.includes(data.channel)) {
                        this.channels.push(data.channel);
                    }
                    break;

                case 'unsubscribed':
                    this.channels = this.channels.filter(c => c !== data.channel);
                    break;

                case 'pong':
                    // Heartbeat response
                    break;

                // Notification types
                case 'booking_approved':
                case 'booking_rejected':
                case 'booking_cancelled':
                case 'booking_reminder':
                case 'new_booking':
                case 'notification':
                case 'system':
                case 'maintenance':
                    this.handleNotification(data);
                    break;
            }
        } catch (error) {
            this.log('Message parse error:', error);
        }
    }

    /**
     * Handle notification messages
     */
    handleNotification(data) {
        this.emit('notification', data);

        // Show browser notification if permitted
        if (Notification.permission === 'granted') {
            this.showBrowserNotification(data);
        }

        // Show toast notification
        this.showToast(data);
    }

    /**
     * Show browser notification
     */
    showBrowserNotification(data) {
        const notification = new Notification(data.title, {
            body: data.message,
            icon: '/images/notification-icon.png',
            tag: data.type,
        });

        notification.onclick = () => {
            window.focus();
            if (data.booking && data.booking.id) {
                window.location.href = `/booking/view?id=${data.booking.id}`;
            }
        };
    }

    /**
     * Show toast notification
     */
    showToast(data) {
        // Check if Bootstrap toast is available
        if (typeof bootstrap !== 'undefined' && typeof bootstrap.Toast !== 'undefined') {
            this.showBootstrapToast(data);
        } else {
            // Fallback to custom toast
            this.showCustomToast(data);
        }
    }

    /**
     * Show Bootstrap 5 toast
     */
    showBootstrapToast(data) {
        const colorClass = {
            success: 'bg-success',
            danger: 'bg-danger',
            warning: 'bg-warning',
            info: 'bg-info',
        }[data.color] || 'bg-primary';

        const toastHtml = `
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header ${colorClass} text-white">
                    <i class="bi bi-${data.icon || 'bell'} me-2"></i>
                    <strong class="me-auto">${data.title}</strong>
                    <small>just now</small>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${data.message}
                </div>
            </div>
        `;

        // Create or get toast container
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '1100';
            document.body.appendChild(container);
        }

        // Add toast to container
        container.insertAdjacentHTML('beforeend', toastHtml);

        // Get the toast element and show it
        const toastElement = container.lastElementChild;
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000,
        });
        toast.show();

        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    /**
     * Show custom toast (fallback)
     */
    showCustomToast(data) {
        const toast = document.createElement('div');
        toast.className = `custom-toast custom-toast-${data.color || 'info'}`;
        toast.innerHTML = `
            <div class="custom-toast-header">
                <strong>${data.title}</strong>
                <button class="custom-toast-close">&times;</button>
            </div>
            <div class="custom-toast-body">${data.message}</div>
        `;

        // Add styles if not already present
        if (!document.getElementById('custom-toast-styles')) {
            const styles = document.createElement('style');
            styles.id = 'custom-toast-styles';
            styles.textContent = `
                .custom-toast {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    min-width: 300px;
                    max-width: 400px;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 1100;
                    animation: slideIn 0.3s ease;
                }
                .custom-toast-success { border-left: 4px solid #28a745; }
                .custom-toast-danger { border-left: 4px solid #dc3545; }
                .custom-toast-warning { border-left: 4px solid #ffc107; }
                .custom-toast-info { border-left: 4px solid #17a2b8; }
                .custom-toast-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 10px 15px;
                    border-bottom: 1px solid #eee;
                }
                .custom-toast-body { padding: 15px; }
                .custom-toast-close {
                    background: none;
                    border: none;
                    font-size: 20px;
                    cursor: pointer;
                    opacity: 0.5;
                }
                .custom-toast-close:hover { opacity: 1; }
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(styles);
        }

        document.body.appendChild(toast);

        // Close button
        toast.querySelector('.custom-toast-close').onclick = () => toast.remove();

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.animation = 'slideIn 0.3s ease reverse';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    /**
     * Schedule reconnection attempt
     */
    scheduleReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            this.log('Max reconnect attempts reached');
            return;
        }

        this.reconnectAttempts++;
        const delay = this.reconnectInterval * Math.pow(1.5, this.reconnectAttempts - 1);
        
        this.log(`Reconnecting in ${delay}ms (attempt ${this.reconnectAttempts})`);
        
        setTimeout(() => this.connect(), delay);
    }

    /**
     * Send message to server
     */
    send(action, data = {}) {
        if (!this.isConnected) {
            this.log('Not connected');
            return false;
        }

        const message = JSON.stringify({ action, ...data });
        this.ws.send(message);
        this.log('Sent:', { action, ...data });
        return true;
    }

    /**
     * Authenticate with token
     */
    authenticate(token) {
        this.token = token;
        return this.send('authenticate', { token });
    }

    /**
     * Subscribe to channel
     */
    subscribe(channel) {
        return this.send('subscribe', { channel });
    }

    /**
     * Unsubscribe from channel
     */
    unsubscribe(channel) {
        return this.send('unsubscribe', { channel });
    }

    /**
     * Send ping to keep connection alive
     */
    ping() {
        return this.send('ping');
    }

    /**
     * Register event handler
     */
    on(event, handler) {
        if (this.handlers[event]) {
            this.handlers[event].push(handler);
        }
        return this;
    }

    /**
     * Remove event handler
     */
    off(event, handler) {
        if (this.handlers[event]) {
            this.handlers[event] = this.handlers[event].filter(h => h !== handler);
        }
        return this;
    }

    /**
     * Register message type handler
     */
    onMessage(type, handler) {
        if (!this.messageHandlers[type]) {
            this.messageHandlers[type] = [];
        }
        this.messageHandlers[type].push(handler);
        return this;
    }

    /**
     * Emit event
     */
    emit(event, data) {
        if (this.handlers[event]) {
            this.handlers[event].forEach(handler => handler(data));
        }
    }

    /**
     * Request browser notification permission
     */
    requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }

    /**
     * Log message (debug mode only)
     */
    log(...args) {
        if (this.debug) {
            console.log('[WebSocket]', ...args);
        }
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MRBWebSocket;
}

// Auto-initialize if token is available
document.addEventListener('DOMContentLoaded', function() {
    // Check for auth token in meta tag or global variable
    const tokenMeta = document.querySelector('meta[name="ws-token"]');
    const token = tokenMeta ? tokenMeta.content : (window.wsToken || null);

    if (token) {
        window.mrbWebSocket = new MRBWebSocket({
            token: token,
            debug: window.location.hostname === 'localhost',
        });
        window.mrbWebSocket.connect();
        window.mrbWebSocket.requestNotificationPermission();
    }
});
