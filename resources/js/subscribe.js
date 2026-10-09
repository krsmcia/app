function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);

    const base64 = (base64String + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');

    return Uint8Array.from(
        atob(base64),
        char => char.charCodeAt(0)
    );
}

async function pushRequest(url, method, body = undefined) {
    const response = await fetch(url, {
        method,
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                .content,
        },
        ...(body ? { body: JSON.stringify(body) } : {}),
    });

    if (!response.ok) {
        throw new Error(
            response.status === 401
                ? 'Please log in again.'
                : `Request failed (${response.status}).`
        );
    }

    return response.json();
}

document.addEventListener('alpine:init', () => {
    Alpine.data('pushNotificationToggle', () => ({
        enabled: false,
        loading: true,
        supported: true,
        status: 'Checking notification status...',

        async init() {
            this.supported =
                'serviceWorker' in navigator &&
                'PushManager' in window &&
                'Notification' in window;

            if (!this.supported) {
                this.status = 'Push notifications are not supported.';
                this.loading = false;
                return;
            }

            try {
                const registration = await navigator.serviceWorker.ready;
                const subscription =
                    await registration.pushManager.getSubscription();

                this.enabled = !!subscription;
                this.status = this.enabled
                    ? 'Notifications are enabled on this device.'
                    : Notification.permission === 'denied'
                        ? 'Notifications are blocked in browser settings.'
                        : 'Notifications are disabled on this device.';
            } catch (error) {
                this.status = 'Unable to check notification status.';
            } finally {
                this.loading = false;
            }
        },

        async toggle() {
            if (this.loading) return;

            this.loading = true;

            try {
                if (this.enabled) {
                    await this.disable();
                } else {
                    await this.enable();
                }
            } catch (error) {
                this.status = error.message || 'Something went wrong.';
            } finally {
                this.loading = false;
            }
        },

        async enable() {
            if (Notification.permission === 'denied') {
                throw new Error(
                    'Allow notifications in your browser settings first.'
                );
            }

            const permission = await Notification.requestPermission();

            if (permission !== 'granted') {
                throw new Error('Notification permission was not granted.');
            }

            // Register SW if it hasn't been registered yet.
            await navigator.serviceWorker.register('/sw.js');

            const registration = await navigator.serviceWorker.ready;

            const keyResponse = await pushRequest(
                '/push/vapid-public-key',
                'GET'
            );

            if (!keyResponse.publicKey) {
                throw new Error('VAPID public key is missing.');
            }

            let subscription =
                await registration.pushManager.getSubscription();

            if (!subscription) {
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(
                        keyResponse.publicKey
                    ),
                });
            }

            const json = subscription.toJSON();

            try {
                await pushRequest('/push/subscribe', 'POST', {
                    endpoint: json.endpoint,
                    keys: {
                        p256dh: json.keys.p256dh,
                        auth: json.keys.auth,
                    },
                });
            } catch (error) {
                // Avoid leaving an unregistered local subscription behind.
                await subscription.unsubscribe();
                throw error;
            }

            this.enabled = true;
            this.status = 'Notifications are enabled on this device.';
        },

        async disable() {
            const registration = await navigator.serviceWorker.ready;
            const subscription =
                await registration.pushManager.getSubscription();

            if (subscription) {
                // Delete the server record before unsubscribing locally.
                await pushRequest('/push/unsubscribe', 'DELETE', {
                    endpoint: subscription.endpoint,
                });

                await subscription.unsubscribe();
            }

            this.enabled = false;
            this.status = 'Notifications are disabled on this device.';
        },
    }));
});