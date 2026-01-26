/**
 * Firebase Cloud Messaging Service Worker
 * 
 * Handles background push notifications for web browsers
 * 
 * @author PBRI Digital Technology & AI Division
 * @version 1.0
 */

// Firebase SDK
importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js');

// Firebase configuration (will be replaced during deployment)
const firebaseConfig = {
    apiKey: "YOUR_FIREBASE_API_KEY",
    authDomain: "YOUR_PROJECT_ID.firebaseapp.com",
    projectId: "YOUR_PROJECT_ID",
    storageBucket: "YOUR_PROJECT_ID.appspot.com",
    messagingSenderId: "YOUR_SENDER_ID",
    appId: "YOUR_APP_ID"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Initialize messaging
const messaging = firebase.messaging();

// Cache name for offline support
const CACHE_NAME = 'meeting-room-v1';

// URLs to cache
const urlsToCache = [
    '/',
    '/css/site.css',
    '/js/main.js',
    '/images/notification-icon.png',
    '/images/badge-icon.png'
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
    console.log('[SW] Installing service worker...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[SW] Caching app shell');
                return cache.addAll(urlsToCache);
            })
            .catch((err) => {
                console.error('[SW] Cache install failed:', err);
            })
    );
    // Skip waiting and activate immediately
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('[SW] Activating service worker...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[SW] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    // Take control of all clients
    return self.clients.claim();
});

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('[SW] Received background message:', payload);
    
    const notificationTitle = payload.notification?.title || 'การแจ้งเตือนใหม่';
    const notificationOptions = {
        body: payload.notification?.body || '',
        icon: payload.notification?.icon || '/images/notification-icon.png',
        badge: '/images/badge-icon.png',
        tag: payload.data?.tag || 'default',
        data: payload.data || {},
        requireInteraction: payload.data?.requireInteraction === 'true',
        actions: getNotificationActions(payload.data?.type),
        vibrate: [200, 100, 200],
        timestamp: Date.now()
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Get notification actions based on type
function getNotificationActions(type) {
    const actions = {
        'booking_created': [
            { action: 'view', title: 'ดูรายละเอียด', icon: '/images/icons/view.png' }
        ],
        'booking_approved': [
            { action: 'view', title: 'ดูการจอง', icon: '/images/icons/view.png' },
            { action: 'calendar', title: 'เพิ่มลงปฏิทิน', icon: '/images/icons/calendar.png' }
        ],
        'booking_rejected': [
            { action: 'view', title: 'ดูรายละเอียด', icon: '/images/icons/view.png' },
            { action: 'rebook', title: 'จองใหม่', icon: '/images/icons/retry.png' }
        ],
        'booking_reminder': [
            { action: 'view', title: 'ดูการจอง', icon: '/images/icons/view.png' },
            { action: 'directions', title: 'นำทาง', icon: '/images/icons/map.png' }
        ],
        'pending_approval': [
            { action: 'approve', title: 'อนุมัติ', icon: '/images/icons/check.png' },
            { action: 'view', title: 'ดูรายละเอียด', icon: '/images/icons/view.png' }
        ]
    };
    
    return actions[type] || [{ action: 'view', title: 'ดูรายละเอียด' }];
}

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked:', event.action);
    
    event.notification.close();
    
    const data = event.notification.data || {};
    let targetUrl = '/';
    
    switch (event.action) {
        case 'view':
            if (data.booking_id) {
                targetUrl = `/booking/view?id=${data.booking_id}`;
            }
            break;
        case 'approve':
            if (data.booking_id) {
                targetUrl = `/approval/view?id=${data.booking_id}`;
            }
            break;
        case 'calendar':
            // Add to calendar logic handled by main app
            if (data.booking_id) {
                targetUrl = `/booking/view?id=${data.booking_id}&addToCalendar=1`;
            }
            break;
        case 'rebook':
            if (data.room_id) {
                targetUrl = `/booking/create?room_id=${data.room_id}`;
            }
            break;
        case 'directions':
            if (data.room_id) {
                targetUrl = `/room/view?id=${data.room_id}#location`;
            }
            break;
        default:
            // Default click action
            targetUrl = data.click_action || '/';
    }
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((windowClients) => {
                // Check if there's already a window open
                for (let client of windowClients) {
                    if (client.url.includes(self.registration.scope) && 'focus' in client) {
                        client.navigate(targetUrl);
                        return client.focus();
                    }
                }
                // Open new window
                if (clients.openWindow) {
                    return clients.openWindow(targetUrl);
                }
            })
    );
});

// Handle notification close
self.addEventListener('notificationclose', (event) => {
    console.log('[SW] Notification closed:', event.notification.tag);
    
    // Analytics or cleanup if needed
    const data = event.notification.data || {};
    
    // Send analytics event
    if (self.registration.scope) {
        fetch('/api/analytics/notification-dismissed', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                notification_id: data.notification_id,
                type: data.type,
                dismissed_at: new Date().toISOString()
            })
        }).catch(() => {
            // Silently fail if offline
        });
    }
});

// Handle push event (alternative to Firebase messaging)
self.addEventListener('push', (event) => {
    console.log('[SW] Push event received');
    
    if (!event.data) {
        console.log('[SW] No data in push event');
        return;
    }
    
    let payload;
    try {
        payload = event.data.json();
    } catch (e) {
        payload = { body: event.data.text() };
    }
    
    const title = payload.title || 'การแจ้งเตือนใหม่';
    const options = {
        body: payload.body || '',
        icon: payload.icon || '/images/notification-icon.png',
        badge: '/images/badge-icon.png',
        data: payload.data || {},
        tag: payload.tag || 'default',
        requireInteraction: false,
        silent: false
    };
    
    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Message handler for communication with main app
self.addEventListener('message', (event) => {
    console.log('[SW] Message received:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: '1.0.0' });
    }
    
    if (event.data && event.data.type === 'CLEAR_CACHE') {
        caches.delete(CACHE_NAME).then(() => {
            event.ports[0].postMessage({ cleared: true });
        });
    }
});

// Fetch event - network first with cache fallback for API, cache first for static
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // API requests - network first
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(
            fetch(request)
                .catch(() => {
                    return new Response(JSON.stringify({
                        error: 'offline',
                        message: 'คุณกำลังออฟไลน์'
                    }), {
                        status: 503,
                        headers: { 'Content-Type': 'application/json' }
                    });
                })
        );
        return;
    }
    
    // Static assets - cache first
    event.respondWith(
        caches.match(request)
            .then((response) => {
                if (response) {
                    return response;
                }
                return fetch(request).then((response) => {
                    // Don't cache non-successful responses
                    if (!response || response.status !== 200 || response.type !== 'basic') {
                        return response;
                    }
                    
                    // Cache static assets
                    if (url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|svg|woff|woff2)$/)) {
                        const responseToCache = response.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseToCache);
                        });
                    }
                    
                    return response;
                });
            })
    );
});

console.log('[SW] Service worker loaded');
