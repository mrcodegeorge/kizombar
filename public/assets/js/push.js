// Kizo SOP Manager — Push Notification Client
// VAPID Public Key (replace with your own from: https://web-push-codelab.glitch.me/)
const VAPID_PUBLIC_KEY = 'BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LFgDzkrxZJjSgSnfckjBJuBkr3qBuyAjqh2TDqoSr0=';

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
    return outputArray;
}

async function requestPushPermission() {
    if (!('PushManager' in window) || !('serviceWorker' in navigator)) {
        showToast('Push notifications not supported on this browser.');
        return;
    }
    try {
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            showToast('Notifications blocked. Please enable in browser settings.');
            return;
        }
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
        });
        // Send subscription to server
        await fetch('index.php?action=save_push_subscription', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(subscription)
        });
        // Hide bell, show confirmation
        const bell = document.getElementById('notifBell');
        if (bell) bell.classList.add('d-none');
        showToast('🔔 Notifications enabled!');
    } catch (err) {
        console.error('Push subscription error:', err);
        showToast('Could not enable notifications.');
    }
}

// Auto-show bell if not subscribed
document.addEventListener('DOMContentLoaded', async () => {
    const bell = document.getElementById('notifBell');
    if (!bell || !('serviceWorker' in navigator) || !('PushManager' in window)) return;
    try {
        const reg = await navigator.serviceWorker.ready;
        const sub = await reg.pushManager.getSubscription();
        if (!sub && Notification.permission !== 'denied') {
            bell.classList.remove('d-none');
        }
    } catch (e) {}
});
