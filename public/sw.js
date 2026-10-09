self.addEventListener('push', function (event) {
    let data = {};

    if (event.data) {
        try {
            data = event.data.json();
        } catch (error) {
            console.error('Failed to parse push notification:', error);
            data = {
                title: 'Notification',
                body: event.data.text()
            };
        }
    }

    const notificationData = data.data || {};

    const options = {
        body: data.body || 'You have a new notification.',
        icon: data.icon || '/approved-icon.png',
        badge: data.badge || '/badge.png',
        vibrate: data.vibrate || [300, 100, 300, 100, 500],
        data: {
            url: notificationData.url || data.url || '/dashboard',
            action: notificationData.action || null
        },
        requireInteraction: true,
        tag: data.tag || 'cia-notification',
        actions: [
            {
                action: 'view_account',
                title: 'View account'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(
            data.title || 'CIA Notification',
            options
        )
    );
});


self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const notificationData = event.notification.data || {};
    const targetUrl = new URL(
        notificationData.url || '/dashboard',
        self.location.origin
    );

    // 다른 도메인으로의 이동을 방지
    if (targetUrl.origin !== self.location.origin) {
        return;
    }

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(function (clientList) {
            for (const client of clientList) {
                if (client.url === targetUrl.href && 'focus' in client) {
                    return client.focus();
                }
            }

            // 같은 도메인의 열린 창이 있으면 해당 창으로 이동
            for (const client of clientList) {
                if ('navigate' in client && 'focus' in client) {
                    return client.navigate(targetUrl.href)
                        .then(function (navigatedClient) {
                            return navigatedClient
                                ? navigatedClient.focus()
                                : clients.openWindow(targetUrl.href);
                        });
                }
            }

            return clients.openWindow(targetUrl.href);
        })
    );
});


self.addEventListener('notificationclose', function (event) {
    // 필요하면 알림 닫힘 처리 추가
});