const CACHE = 'dsz-v1';
const FILES = [
    '/',
    '/css/style.css',
    '/logo.png',
    '/logo1.png',
    '/manifest.json'
];

self.addEventListener('install', function(e) {
    e.waitUntil(
        caches.open(CACHE).then(function(c) { return c.addAll(FILES); })
    );
    self.skipWaiting();
});

self.addEventListener('activate', function(e) {
    e.waitUntil(
        caches.keys().then(function(keys) {
            return Promise.all(keys.filter(function(k) { return k !== CACHE; }).map(function(k) { return caches.delete(k); }));
        })
    );
});

self.addEventListener('fetch', function(e) {
    e.respondWith(
        fetch(e.request).catch(function() { return caches.match(e.request); })
    );
});
