/* =====================================================
   VMS Service Worker — PWA offline support
   Dikembangkan oleh Trijaya Solution
   ===================================================== */
const CACHE_NAME = 'vms-cache-v2';
const OFFLINE_URL = '/offline';

/* Aset inti untuk cache-first */
const CORE_ASSETS = [
  '/offline',
  '/manifest.json',
  '/assets/css/landing.css',
  '/assets/css/app.css',
  '/assets/css/login.css',
  '/assets/js/app.js',
  '/assets/js/landing.js',
  '/assets/js/login.js',
  '/assets/js/dashboard.js',
  '/assets/vendor/chart.umd.min.js',
  '/assets/img/logo.svg',
  '/assets/img/icons/icon-192.png',
  '/assets/img/icons/icon-512.png'
];

/* Install: pre-cache aset inti */
self.addEventListener('install', function (event) {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function (cache) {
        return cache.addAll(CORE_ASSETS.map(u => new Request(u, { cache: 'reload' })));
      })
      .catch(function () { /* install tetap lanjut walau ada aset gagal */ })
      .then(function () {
        return self.skipWaiting();
      })
  );
});

/* Activate: bersihkan cache lama */
self.addEventListener('activate', function (event) {
  event.waitUntil(
    caches.keys().then(function (keys) {
      return Promise.all(
        keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
      );
    }).then(function () {
      return self.clients.claim();
    })
  );
});

/* Fetch strategy:
   - Navigasi halaman: network-first, fallback offline page
   - Aset statis (css/js/img/font): cache-first, update di belakang
   - POST/api: network only */
self.addEventListener('fetch', function (event) {
  const req = event.request;
  if (req.method !== 'GET') return;

  const url = new URL(req.url);
  if (url.origin !== self.location.origin) return; // biarkan CDN lewat network

  // Navigasi halaman
  if (req.mode === 'navigate') {
    event.respondWith(
      fetch(req)
        .then(function (res) {
          const copy = res.clone();
          caches.open(CACHE_NAME).then(c => c.put(req, copy));
          return res;
        })
        .catch(function () {
          return caches.match(req).then(function (cached) {
            return cached || caches.match(OFFLINE_URL);
          });
        })
    );
    return;
  }

  // Statis: cache-first + revalidate
  const isStatic = /\.(css|js|png|jpg|jpeg|svg|webp|woff2?|ico|json)$/.test(url.pathname);
  if (isStatic) {
    event.respondWith(
      caches.match(req).then(function (cached) {
        if (cached) {
          fetch(req).then(function (res) {
            if (res && res.status === 200) {
              caches.open(CACHE_NAME).then(c => c.put(req, res.clone()));
            }
          }).catch(() => {});
          return cached;
        }
        return fetch(req).then(function (res) {
          if (res && res.status === 200) {
            const copy = res.clone();
            caches.open(CACHE_NAME).then(c => c.put(req, copy));
          }
          return res;
        }).catch(() => caches.match(OFFLINE_URL));
      })
    );
  }
});
