self.addEventListener('push', function (event) {
    if (!event.data) {
        return;
    }

    let data;

    try {
        data = event.data.json();
    } catch {
        data = {
            title: 'CIA Notification',
            body: event.data.text(),
        };
    }

    const options = {
        body: data.body ?? '',
        icon: data.icon ?? '/icon.png',
        badge: data.badge ?? '/badge.png',

        vibrate: [300, 100, 300, 100, 500],

        requireInteraction: true,

        data: {
            url: '/dashboard',
        },
    };

    event.waitUntil(
        self.registration.showNotification(
            data.title ?? 'CIA Notification',
            options
        )
    );
});


self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const url = event.notification.data?.url ?? '/dashboard';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true,
        }).then(function (clientList) {

            // 이미 CIA 사이트가 열려 있다면 해당 창으로 이동
            for (const client of clientList) {
                if ('focus' in client) {
                    return client
                        .navigate(url)
                        .then(() => client.focus());
                }
            }

            // 열려 있는 창이 없다면 새 창
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});