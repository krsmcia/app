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

    const targetUrl = new URL(
        event.notification.data?.url ?? '/dashboard',
        self.location.origin
    ).href;

    event.waitUntil((async () => {
        try {
            const clientList = await self.clients.matchAll({
                type: 'window',
                includeUncontrolled: true,
            });

            // 같은 사이트의 창만 대상으로 처리
            for (const client of clientList) {
                if (new URL(client.url).origin !== self.location.origin) {
                    continue;
                }

                try {
                    await client.navigate(targetUrl);
                    await client.focus();
                    return;
                } catch (error) {
                    // 해당 창이 더 이상 유효하지 않으면 다음 창을 확인
                    console.warn('Could not reuse notification client:', error);
                }
            }

            await self.clients.openWindow(targetUrl);
        } catch (error) {
            console.error('Notification click failed:', error);
        }
    })());
});