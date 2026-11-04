// Service Worker for THEKIEN Fashion PWA / iOS App
const CACHE_NAME = 'thekien-v1';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
    // Pass-through network first
    event.respondWith(
        fetch(event.request).catch(() => caches.match(event.request))
    );
});

