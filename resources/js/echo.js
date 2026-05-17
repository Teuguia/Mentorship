import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const reverbConfig = window.reverbConfig ?? {};
const scheme = reverbConfig.scheme ?? import.meta.env.VITE_REVERB_SCHEME ?? 'https';
const port = reverbConfig.port ?? import.meta.env.VITE_REVERB_PORT;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: reverbConfig.key ?? import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: reverbConfig.host ?? import.meta.env.VITE_REVERB_HOST,
    wsPort: port ?? 80,
    wssPort: port ?? 443,
    forceTLS: scheme === 'https',
    enabledTransports: ['ws', 'wss'],
    auth: {
        headers: {
            'X-CSRF-TOKEN': csrfToken ?? '',
            'X-Requested-With': 'XMLHttpRequest',
        },
    },
});
