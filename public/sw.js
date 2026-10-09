self.addEventListener('push', function(event) {
    const payload = event.data ? event.data.json() : {};
    console.log('Push payload:', payload);
    const options = {
        body: payload.body,
        icon: '/icon.png',
        badge: '/badge.png',
        requireInteraction: true,
        data: {
            url: payload.url || payload.data?.url || '/procurements/requests'
        }
    };
    event.waitUntil(
        self.registration.showNotification(payload.title, options)
    );
});
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil((async () => {
        const url = event.notification.data?.url
            || '/';
        const targetUrl = new URL(url, self.location.origin).href;
        await clients.openWindow(targetUrl);
    })());
});