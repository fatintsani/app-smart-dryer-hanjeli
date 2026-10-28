// Version injected by Vite; Docker serves public/build/sw.js at the stable /sw.js URL.
const BUILD_ID = '__SMART_DRYER_BUILD__';
const CACHE_PREFIX = 'smart-dryer-';
const CACHE_NAME = `${CACHE_PREFIX}${BUILD_ID}`;
const OFFLINE_URL = '/offline.html';
const PRECACHE_ASSETS = [OFFLINE_URL, '/pwa-192x192.png', '/pwa-512x512.png', '/apple-touch-icon.png'];

self.addEventListener('install', (event) => {
  event.waitUntil((async () => {
    const cache = await caches.open(CACHE_NAME);
    await cache.addAll(PRECACHE_ASSETS.map((url) => new Request(url, { cache: 'reload' })));
    await self.skipWaiting();
  })());
});

self.addEventListener('activate', (event) => {
  event.waitUntil((async () => {
    const names = await caches.keys();
    await Promise.all(names.filter((name) => name.startsWith(CACHE_PREFIX) && name !== CACHE_NAME).map((name) => caches.delete(name)));
    await self.clients.claim();
  })());
});

self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);
  // API, authentication, external requests and unversioned assets use the network.
  if (request.method !== 'GET' || url.origin !== self.location.origin) return;

  if (request.mode === 'navigate') {
    event.respondWith((async () => {
      try {
        // Never return old application HTML referencing a previous Vite bundle.
        return await fetch(new Request(request, { cache: 'no-store' }));
      } catch {
        const cache = await caches.open(CACHE_NAME);
        return await cache.match(OFFLINE_URL) || new Response('Offline', { status: 503 });
      }
    })());
    return;
  }

  // Only content-hashed Vite assets are immutable. Never cache API data or /sw.js.
  if (url.pathname.startsWith('/build/assets/')) {
    event.respondWith((async () => {
      const cache = await caches.open(CACHE_NAME);
      const cached = await cache.match(request);
      if (cached) return cached;
      try {
        const response = await fetch(request);
        if (response.ok && response.type === 'basic') {
          await cache.put(request, response.clone());
        }
        return response;
      } catch {
        return new Response('Asset unavailable offline', { status: 503 });
      }
    })());
  }
});

// Web Push Notification Event
self.addEventListener('push', (event) => {
  let data = {
    title: 'Smart Room Dryer Hanjeli',
    body: 'Pembaruan status sistem pengeringan!',
    icon: '/pwa-192x192.png',
    badge: '/pwa-192x192.png',
    tag: 'smart-dryer-alert',
    url: '/monitoring'
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
    icon: data.icon || '/pwa-192x192.png',
    badge: data.badge || '/pwa-192x192.png',
    tag: data.tag || 'smart-dryer-alert',
    vibrate: [200, 100, 200],
    data: {
      url: data.url || '/monitoring'
    },
    actions: [
      { action: 'open', title: 'Buka Monitoring' },
      { action: 'close', title: 'Tutup' }
    ]
  };

  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});

// Notification Click Event
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'close') {
    return;
  }

  const targetUrl = event.notification.data?.url || '/';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      for (const client of clientList) {
        if (client.url.includes(self.location.origin) && 'focus' in client) {
          client.navigate(targetUrl);
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});
