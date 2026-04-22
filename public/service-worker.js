const CACHE_NAME = 'netcom-push-v1';
const STATIC_CACHE = 'netcom-static-v1';

const STATIC_ASSETS = [
    '/',
    '/index.php',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    return self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== STATIC_CACHE)
                    .map((name) => caches.delete(name))
            );
        })
    );
    return self.clients.claim();
});

self.addEventListener('push', (event) => {
    let data = {
        title: 'طلب شحن جديد',
        body: 'لديك طلب شحن جديد',
        icon: '/images/notification-icon.png',
        badge: '/images/badge-icon.png',
        tag: 'recharge-notification',
        renotify: true,
        data: {
            url: '/admin/recharge-requests',
            timestamp: Date.now()
        }
    };

    if (event.data) {
        try {
            const payload = event.data.json();
            data = { ...data, ...payload };
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/images/notification-icon.png',
        badge: data.badge || '/images/badge-icon.png',
        tag: data.tag || 'recharge-notification',
        renotify: data.renotify ?? true,
        data: data.data || { url: '/admin/recharge-requests' },
        vibrate: [200, 100, 200],
        requireInteraction: true,
        actions: [
            {
                action: 'view',
                title: 'عرض'
            },
            {
                action: 'dismiss',
                title: 'إغلاق'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const urlToOpen = event.notification.data?.url || '/admin/recharge-requests';

    if (event.action === 'dismiss') {
        return;
    }

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes('/admin') && 'focus' in client) {
                    client.navigate(urlToOpen);
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});