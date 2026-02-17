/**
 * Push Notification Manager
 * 
 * Handles FCM registration, permission requests, and token management
 * 
 * @author BIzAI
 * @version 1.0
 */

class PushNotificationManager {
    constructor(config = {}) {
        this.firebaseConfig = config.firebaseConfig || null;
        this.vapidKey = config.vapidKey || null;
        this.registerUrl = config.registerUrl || '/notification-settings/register-token';
        this.unregisterUrl = config.unregisterUrl || '/notification-settings/unregister-token';
        this.csrfToken = config.csrfToken || '';
        
        this.messaging = null;
        this.currentToken = null;
        this.isInitialized = false;
        
        // Callbacks
        this.onTokenReceived = config.onTokenReceived || null;
        this.onMessageReceived = config.onMessageReceived || null;
        this.onPermissionDenied = config.onPermissionDenied || null;
    }
    
    /**
     * Initialize Firebase and request permission
     */
    async init() {
        // Check if browser supports notifications
        if (!('Notification' in window)) {
            console.warn('[Push] This browser does not support notifications');
            return false;
        }
        
        // Check for service worker support
        if (!('serviceWorker' in navigator)) {
            console.warn('[Push] Service workers not supported');
            return false;
        }
        
        // Check if Firebase config is available
        if (!this.firebaseConfig || !this.vapidKey) {
            console.warn('[Push] Firebase configuration not provided');
            return false;
        }
        
        try {
            // Initialize Firebase
            if (!firebase.apps.length) {
                firebase.initializeApp(this.firebaseConfig);
            }
            
            this.messaging = firebase.messaging();
            
            // Register service worker
            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            console.log('[Push] Service worker registered:', registration);
            
            // Request permission
            const permission = await this.requestPermission();
            if (permission !== 'granted') {
                console.warn('[Push] Notification permission denied');
                if (this.onPermissionDenied) {
                    this.onPermissionDenied();
                }
                return false;
            }
            
            // Get FCM token
            await this.getToken();
            
            // Listen for messages when app is in foreground
            this.messaging.onMessage((payload) => {
                console.log('[Push] Foreground message received:', payload);
                this.handleForegroundMessage(payload);
            });
            
            // Listen for token refresh
            this.messaging.onTokenRefresh(async () => {
                console.log('[Push] Token refreshed');
                await this.getToken();
            });
            
            this.isInitialized = true;
            console.log('[Push] Initialization complete');
            return true;
            
        } catch (error) {
            console.error('[Push] Initialization error:', error);
            return false;
        }
    }
    
    /**
     * Request notification permission
     */
    async requestPermission() {
        try {
            const permission = await Notification.requestPermission();
            console.log('[Push] Permission status:', permission);
            return permission;
        } catch (error) {
            console.error('[Push] Permission request error:', error);
            return 'denied';
        }
    }
    
    /**
     * Get FCM token
     */
    async getToken() {
        try {
            const token = await this.messaging.getToken({
                vapidKey: this.vapidKey
            });
            
            if (token) {
                console.log('[Push] FCM token:', token.substring(0, 20) + '...');
                
                // Check if token changed
                if (token !== this.currentToken) {
                    this.currentToken = token;
                    
                    // Register token with backend
                    await this.registerToken(token);
                    
                    if (this.onTokenReceived) {
                        this.onTokenReceived(token);
                    }
                }
                
                return token;
            }
            
            console.warn('[Push] No registration token available');
            return null;
            
        } catch (error) {
            console.error('[Push] Error getting token:', error);
            return null;
        }
    }
    
    /**
     * Register token with backend
     */
    async registerToken(token) {
        try {
            const response = await fetch(this.registerUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': this.csrfToken
                },
                body: new URLSearchParams({
                    token: token,
                    provider: 'fcm',
                    platform: this.getPlatform(),
                    device_name: this.getDeviceName()
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                console.log('[Push] Token registered with backend');
                // Store token ID for later use
                localStorage.setItem('push_token_id', result.token_id);
            } else {
                console.warn('[Push] Failed to register token:', result.message);
            }
            
            return result;
            
        } catch (error) {
            console.error('[Push] Register token error:', error);
            return { success: false, error: error.message };
        }
    }
    
    /**
     * Unregister token from backend
     */
    async unregisterToken() {
        if (!this.currentToken) {
            return { success: true };
        }
        
        try {
            // Delete token from FCM
            await this.messaging.deleteToken();
            
            // Unregister from backend
            const response = await fetch(this.unregisterUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': this.csrfToken
                },
                body: new URLSearchParams({
                    token: this.currentToken
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                console.log('[Push] Token unregistered');
                this.currentToken = null;
                localStorage.removeItem('push_token_id');
            }
            
            return result;
            
        } catch (error) {
            console.error('[Push] Unregister token error:', error);
            return { success: false, error: error.message };
        }
    }
    
    /**
     * Handle foreground message
     */
    handleForegroundMessage(payload) {
        // Call custom handler if provided
        if (this.onMessageReceived) {
            this.onMessageReceived(payload);
            return;
        }
        
        // Default: Show browser notification
        const notification = payload.notification;
        if (notification) {
            this.showNotification(notification.title, {
                body: notification.body,
                icon: notification.icon || '/images/notification-icon.png',
                data: payload.data
            });
        }
        
        // Also show toast notification in UI
        this.showToast(payload);
    }
    
    /**
     * Show browser notification
     */
    showNotification(title, options = {}) {
        if (Notification.permission !== 'granted') {
            return;
        }
        
        const notification = new Notification(title, {
            icon: options.icon || '/images/notification-icon.png',
            badge: '/images/badge-icon.png',
            body: options.body || '',
            tag: options.tag || 'default',
            data: options.data || {},
            requireInteraction: options.requireInteraction || false
        });
        
        notification.onclick = () => {
            window.focus();
            if (options.data && options.data.click_action) {
                window.location.href = options.data.click_action;
            }
            notification.close();
        };
        
        // Auto close after 10 seconds
        setTimeout(() => notification.close(), 10000);
        
        return notification;
    }
    
    /**
     * Show toast notification in UI
     */
    showToast(payload) {
        const notification = payload.notification || {};
        const data = payload.data || {};
        
        // Check if Bootstrap toast container exists
        let container = document.getElementById('pushNotificationContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'pushNotificationContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        
        // Create toast element
        const toastId = 'toast_' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = 'toast show';
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="toast-header">
                <img src="/images/notification-icon.png" class="rounded me-2" alt="" width="20" height="20">
                <strong class="me-auto">${this.escapeHtml(notification.title || 'แจ้งเตือน')}</strong>
                <small>เมื่อสักครู่</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${this.escapeHtml(notification.body || '')}
                ${data.booking_id ? `<div class="mt-2"><a href="/booking/view?id=${data.booking_id}" class="btn btn-sm btn-primary">ดูรายละเอียด</a></div>` : ''}
            </div>
        `;
        
        container.appendChild(toast);
        
        // Initialize Bootstrap toast
        if (typeof bootstrap !== 'undefined') {
            const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 8000 });
            bsToast.show();
        }
        
        // Auto remove after animation
        setTimeout(() => {
            toast.remove();
        }, 10000);
        
        // Play notification sound
        this.playNotificationSound();
    }
    
    /**
     * Play notification sound
     */
    playNotificationSound() {
        try {
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(() => {
                // Autoplay might be blocked
            });
        } catch (e) {
            // Ignore audio errors
        }
    }
    
    /**
     * Get platform type
     */
    getPlatform() {
        const ua = navigator.userAgent;
        if (/android/i.test(ua)) {
            return 'android';
        }
        if (/iPad|iPhone|iPod/.test(ua)) {
            return 'ios';
        }
        return 'web';
    }
    
    /**
     * Get device name
     */
    getDeviceName() {
        const ua = navigator.userAgent;
        let deviceName = 'Unknown Device';
        
        // Try to get browser name
        if (ua.indexOf('Chrome') > -1) {
            deviceName = 'Chrome';
        } else if (ua.indexOf('Firefox') > -1) {
            deviceName = 'Firefox';
        } else if (ua.indexOf('Safari') > -1) {
            deviceName = 'Safari';
        } else if (ua.indexOf('Edge') > -1) {
            deviceName = 'Edge';
        }
        
        // Add platform
        if (ua.indexOf('Windows') > -1) {
            deviceName += ' on Windows';
        } else if (ua.indexOf('Mac') > -1) {
            deviceName += ' on macOS';
        } else if (ua.indexOf('Linux') > -1) {
            deviceName += ' on Linux';
        } else if (ua.indexOf('Android') > -1) {
            deviceName += ' on Android';
        } else if (ua.indexOf('iPhone') > -1 || ua.indexOf('iPad') > -1) {
            deviceName += ' on iOS';
        }
        
        return deviceName;
    }
    
    /**
     * Escape HTML
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Check if push notifications are supported
     */
    static isSupported() {
        return (
            'Notification' in window &&
            'serviceWorker' in navigator &&
            'PushManager' in window
        );
    }
    
    /**
     * Get current permission status
     */
    static getPermissionStatus() {
        if (!('Notification' in window)) {
            return 'unsupported';
        }
        return Notification.permission;
    }
}

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PushNotificationManager;
}

// Auto-initialize when DOM ready (if config is available)
document.addEventListener('DOMContentLoaded', () => {
    // Check if Firebase config is provided via global variable
    if (typeof FIREBASE_CONFIG !== 'undefined' && typeof FIREBASE_VAPID_KEY !== 'undefined') {
        window.pushManager = new PushNotificationManager({
            firebaseConfig: FIREBASE_CONFIG,
            vapidKey: FIREBASE_VAPID_KEY,
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || ''
        });
        
        // Auto-initialize if user has granted permission before
        if (Notification.permission === 'granted') {
            window.pushManager.init();
        }
    }
});
