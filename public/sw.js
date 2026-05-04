// Kizo SOP Manager — Service Worker v1.0
const CACHE_NAME = 'kizo-sop-v1';
const OFFLINE_URL = '/offline.html';

const PRECACHE_ASSETS = [
    '/',
    '/index.php?action=dashboard',
    '/assets/css/style.css',
    '/assets/js/app.js',
    '/offline.html',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
    'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap'
];

// Install: cache core assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(PRECACHE_ASSETS).catch(() => {
                // Continue even if some CDN assets fail
                return cache.add(OFFLINE_URL);
            });
        }).then(() => self.skipWaiting())
    );
});

// Activate: clean old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        ).then(() => self.clients.claim())
    );
});

// Fetch: Network-first for PHP pages, Cache-first for static assets
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);
    const isNavigate = event.request.mode === 'navigate';
    const isStatic = /\.(css|js|png|jpg|jpeg|svg|woff2?|ico)$/.test(url.pathname);

    if (isStatic) {
        // Cache-first for assets
        event.respondWith(
            caches.match(event.request).then(cached => {
                return cached || fetch(event.request).then(res => {
                    const clone = res.clone();
                    caches.open(CACHE_NAME).then(c => c.put(event.request, clone));
                    return res;
                });
            })
        );
    } else if (isNavigate) {
        // Network-first for pages, fallback to offline
        event.respondWith(
            fetch(event.request).catch(() =>
                caches.match(event.request).then(cached => cached || caches.match(OFFLINE_URL))
            )
        );
    }
});

// Push Notification Handler
self.addEventListener('push', event => {
    const data = event.data ? event.data.json() : {};
    const title = data.title || 'Kizo SOP Manager';
    const options = {
        body: data.body || 'You have a pending task.',
        icon: '/assets/icons/icon-192.png',
        badge: '/assets/icons/icon-192.png',
        vibrate: [100, 50, 100],
        data: { url: data.url || '/index.php?action=dashboard' },
        actions: [
            { action: 'open', title: 'View Task' },
            { action: 'dismiss', title: 'Dismiss' }
        ]
    };
    event.waitUntil(self.registration.showNotification(title, options));
});

// Notification click handler
self.addEventListener('notificationclick', event => {
    event.notification.close();
    if (event.action !== 'dismiss') {
        const url = event.notification.data.url;
        event.waitUntil(clients.openWindow(url));
    }
});
