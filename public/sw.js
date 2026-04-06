const CACHE_NAME = 'sinergi-pas-v1';
const urlsToCache = [
    '/',
    '/logo1.png',
    '/manifest.json'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlsToCache);
            })
            .catch(err => console.log('SW Cache error during install', err))
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                if (event.request.mode === 'navigate') {
                    return caches.match('/');
                }
            })
    );
});
