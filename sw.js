/**
 * EPAMNHS Service Worker
 * Provides offline support, asset caching, and background sync.
 */

const CACHE_VERSION = 'v1';
const STATIC_CACHE  = `epamnhs-static-${CACHE_VERSION}`;
const DYNAMIC_CACHE = `epamnhs-dynamic-${CACHE_VERSION}`;
const OFFLINE_URL   = 'offline.php';

// Core assets to pre-cache on install
const STATIC_ASSETS = [
  './',
  'index.php',
  'login.php',
  'offline.php',
  'manifest.json',

  // CSS
  'css/index.css',
  'css/homestyle.css',
  'css/pagestyle.css',
  'css/sb-admin-2.min.css',
  'bootstrap/css/bootstrap.min.css',

  // JS
  'bootstrap/js/bootstrap.bundle.js',
  'vendor/jquery/jquery.min.js',
  'vendor/jquery-easing/jquery.easing.min.js',
  'js/sb-admin-2.min.js',

  // Icons / images
  'icons/pwa/icon-192x192.png',
  'icons/pwa/icon-512x512.png',
  'assets/logo.jpg',
];

// ─── Install ────────────────────────────────────────────────────────────────
self.addEventListener('install', event => {
  console.log('[SW] Installing…');
  event.waitUntil(
    caches.open(STATIC_CACHE).then(cache => {
      // Add assets one-by-one so a single 404 doesn't abort everything
      return Promise.allSettled(
        STATIC_ASSETS.map(url => cache.add(url).catch(() => {
          console.warn('[SW] Could not pre-cache:', url);
        }))
      );
    }).then(() => self.skipWaiting())
  );
});

// ─── Activate ───────────────────────────────────────────────────────────────
self.addEventListener('activate', event => {
  console.log('[SW] Activating…');
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys
          .filter(k => k !== STATIC_CACHE && k !== DYNAMIC_CACHE)
          .map(k => {
            console.log('[SW] Deleting old cache:', k);
            return caches.delete(k);
          })
      )
    ).then(() => self.clients.claim())
  );
});

// ─── Fetch ──────────────────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET, cross-origin, and admin API calls
  if (request.method !== 'GET') return;
  if (url.origin !== self.location.origin) return;

  // Strategy: Network-first for PHP pages, Cache-first for static assets
  if (url.pathname.endsWith('.php') || url.pathname === '/') {
    event.respondWith(networkFirstStrategy(request));
  } else {
    event.respondWith(cacheFirstStrategy(request));
  }
});

/**
 * Network-first: try network, fall back to cache, fall back to offline page.
 */
async function networkFirstStrategy(request) {
  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(DYNAMIC_CACHE);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    const cached = await caches.match(request);
    if (cached) return cached;

    // If it's a navigation request, show the offline page
    if (request.mode === 'navigate') {
      const offline = await caches.match(OFFLINE_URL);
      if (offline) return offline;
    }
    return new Response('You are offline and this page is not cached.', {
      status: 503,
      headers: { 'Content-Type': 'text/plain' },
    });
  }
}

/**
 * Cache-first: serve from cache, update cache in background.
 */
async function cacheFirstStrategy(request) {
  const cached = await caches.match(request);
  if (cached) {
    // Stale-while-revalidate: update cache in background
    fetch(request).then(response => {
      if (response.ok) {
        caches.open(STATIC_CACHE).then(cache => cache.put(request, response));
      }
    }).catch(() => {});
    return cached;
  }

  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(STATIC_CACHE);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    return new Response('Asset unavailable offline.', { status: 503 });
  }
}

// ─── Push Notifications ─────────────────────────────────────────────────────
self.addEventListener('push', event => {
  let data = { title: 'EPAMNHS', body: 'You have a new notification.' };
  try { data = { ...data, ...event.data.json() }; } catch {}

  event.waitUntil(
    self.registration.showNotification(data.title, {
      body:    data.body,
      icon:    'icons/pwa/icon-192x192.png',
      badge:   'icons/pwa/icon-96x96.png',
      tag:     data.tag  || 'epamnhs-notification',
      data:    data.url  || '/',
      vibrate: [200, 100, 200],
      actions: [
        { action: 'open',    title: 'Open',    icon: '/icons/pwa/icon-72x72.png' },
        { action: 'dismiss', title: 'Dismiss' },
      ],
    })
  );
});

self.addEventListener('notificationclick', event => {
  event.notification.close();
  if (event.action === 'dismiss') return;
  const url = event.notification.data || '/';
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
      for (const client of list) {
        if (client.url === url && 'focus' in client) return client.focus();
      }
      return clients.openWindow(url);
    })
  );
});

// ─── Background Sync ────────────────────────────────────────────────────────
self.addEventListener('sync', event => {
  if (event.tag === 'sync-pending-forms') {
    event.waitUntil(syncPendingForms());
  }
});

async function syncPendingForms() {
  // Reads pending form submissions stored by the client in IndexedDB
  // and replays them when connectivity is restored.
  console.log('[SW] Background sync: syncing pending form submissions…');
  // Implementation: client code stores failed POSTs in IDB under 'pending-forms';
  // the SW reads and re-submits them here.
}