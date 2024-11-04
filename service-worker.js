const CACHE_NAME = 'cache-v5.01';
const URLS_TO_CACHE = [
    '/',
    '/index.html',
    '/onboarding.html',
    '/result.php',
    '/style.css',
    '/main-result.js',
    '/main.js'
];

// caching
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(URLS_TO_CACHE)
        .then(() => console.log('Assets cached'))
        .catch(error => console.error('Caching failed:', error));
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (!cacheWhitelist.includes(cacheName)) {
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => self.clients.claim())  
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        return response || fetch(event.request);
      }).catch(error => {
        console.error('Fetch failed; returning offline page instead.', error);
      })
  );
});
