import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

//window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
//     wsHost: import.meta.env.VITE_PUSHER_HOST || window.location.hostname,
//     wsPort: import.meta.env.VITE_PUSHER_PORT || 6005,
//     forceTLS: import.meta.env.VITE_PUSHER_SCHEME === 'https',
//     disableStats: true,
// });

// document.addEventListener('DOMContentLoaded', function () {
//     // console.log('Echo:', window.Echo); // Verificar que Echo está definido
//     if (window.Echo) {
//         window.Echo.channel('pagina-recarga')
//     } else {
//         console.error('Echo no está definido');
//     }
// });
