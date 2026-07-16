// sw.js
self.addEventListener('install', function(event) {
  event.waitUntil(
    caches.open('pwa-cache').then(function(cache) {
      return cache.addAll([
        '/mobile/login',
        '/assets/styles/bootstrap.css',
        '/assets/styles/style.css',
        '/assets/fonts/css/fontawesome-all.min.css',
        '/assets/img/favicon.png'
      ]);
    })
  );
});

self.addEventListener('fetch', function(event) {
  event.respondWith(
    caches.match(event.request).then(function(response) {
      return response || fetch(event.request);
    })
  );
});