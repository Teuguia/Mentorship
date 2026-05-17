import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const metaContent = (name) => document.querySelector(`meta[name="${name}"]`)?.content || undefined;
const reverbConfig = window.reverbConfig ?? {};
const key = reverbConfig.key ?? metaContent('reverb-app-key') ?? import.meta.env.VITE_REVERB_APP_KEY;
const host = reverbConfig.host ?? metaContent('reverb-host') ?? import.meta.env.VITE_REVERB_HOST;
const scheme = reverbConfig.scheme ?? metaContent('reverb-scheme') ?? import.meta.env.VITE_REVERB_SCHEME ?? 'https';
const port = reverbConfig.port ?? metaContent('reverb-port') ?? import.meta.env.VITE_REVERB_PORT;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

if (key && host) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key,
        wsHost: host,
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
} else {
    window.Echo = null;
}
