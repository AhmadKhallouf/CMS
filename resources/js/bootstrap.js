import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Log to confirm scripts are loaded
console.log('Pusher loaded:', !!window.Pusher);
console.log('Echo loading...');

// Only initialize Echo if VITE_REVERB_APP_KEY exists
if (import.meta.env.VITE_REVERB_APP_KEY) {
    // Defaults that work on laptops + phones on the same LAN:
    // - If you don't provide VITE_REVERB_HOST, we connect to the same host serving the page
    //   (so the phone connects to your laptop IP, not "localhost" on the phone).
    // - If you don't provide VITE_REVERB_SCHEME, we follow the page scheme (http/https).
    const defaultHost = window.location.hostname;
    const defaultScheme = window.location.protocol.replace(':', ''); // "http" | "https"

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST || defaultHost,
        wsPort: import.meta.env.VITE_REVERB_PORT || 8081,
        wssPort: import.meta.env.VITE_REVERB_PORT || 8081,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? defaultScheme) === 'https',
        enabledTransports: ['ws', 'wss'],
        disableStats: true,
    });

    console.log('✅ Echo initialized successfully with:', {
        key: import.meta.env.VITE_REVERB_APP_KEY,
        host: import.meta.env.VITE_REVERB_HOST || defaultHost,
        port: import.meta.env.VITE_REVERB_PORT || 8081,
        scheme: import.meta.env.VITE_REVERB_SCHEME || defaultScheme,
    });

    // Extra connection diagnostics (especially useful on phones/LAN)
    try {
        const pusher = window.Echo?.connector?.pusher;
        if (pusher?.connection) {
            pusher.connection.bind('state_change', (states) => {
                console.log('🔌 Reverb connection state_change:', states);
            });
            pusher.connection.bind('connected', () => {
                console.log('✅ Reverb connected');
            });
            pusher.connection.bind('disconnected', () => {
                console.log('⚠️ Reverb disconnected');
            });
            pusher.connection.bind('error', (err) => {
                console.error('❌ Reverb connection error:', err);
            });
        } else {
            console.warn('⚠️ No pusher connection object found on Echo connector');
        }
    } catch (e) {
        console.error('❌ Failed to attach Reverb diagnostics:', e);
    }
} else {
    console.error('❌ Echo not initialized - Missing VITE_REVERB_APP_KEY');
    console.log('Available env vars:', import.meta.env);
}