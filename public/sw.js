// Service Worker for PSCRanker PWA
const CACHE_NAME = 'pscranker-v2';
const STATIC_ASSETS = [
    '/',
    '/manifest.json',
    '/images/mascot.jpg',
    '/images/meme_card.jpg'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.log('Cache addAll non-fatal error:', err);
            });
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    // Only handle GET requests
    if (event.request.method !== 'GET') return;

    // Skip chrome-extension or non-http requests
    if (!event.request.url.startsWith('http')) return;

    // Cache-first for images & static assets
    if (event.request.destination === 'image' || event.request.destination === 'font') {
        event.respondWith(
            caches.match(event.request).then((cachedResponse) => {
                if (cachedResponse) return cachedResponse;
                return fetch(event.request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseClone);
                        });
                    }
                    return networkResponse;
                }).catch(() => caches.match('/images/mascot.jpg'));
            })
        );
    } else {
        // Network-first with fallback to cache for HTML navigation & API
        event.respondWith(
            fetch(event.request).then((response) => {
                return response;
            }).catch(() => {
                return caches.match(event.request).then((cached) => {
                    return cached || caches.match('/');
                });
            })
        );
    }
});
