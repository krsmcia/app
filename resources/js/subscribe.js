
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);

    const base64 = (base64String + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);

    return Uint8Array.from(
        [...rawData].map((char) => char.charCodeAt(0))
    );
}

window.subscribeWebPush = async function (publicKey) {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        alert('This browser does not support Web Push.');
        return;
    }

    const permission = await Notification.requestPermission();

    if (permission !== 'granted') {
        alert('Please allow browser notifications.');
        return;
    }

    const registration = await navigator.serviceWorker.register('/sw.js');

    await navigator.serviceWorker.ready;

    let subscription = await registration.pushManager.getSubscription();

    if (!subscription) {
        subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(publicKey),
        });
    }

    const response = await fetch('/push/subscribe', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector(
                'meta[name="csrf-token"]'
            ).content,
        },
        body: JSON.stringify(subscription.toJSON()),
    });

    if (!response.ok) {
        throw new Error('Failed to save push subscription.');
    }

    alert('Web Push subscription completed.');
};