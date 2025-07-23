import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const initializeEcho = () => {
    const token = document.querySelector('meta[name="api-token"]')?.content;

    if (!token) {
        console.error('No API token found for Echo initialization');
        return null;
    }

    return new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
        auth: {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        }
    });
};

window.Echo = initializeEcho();