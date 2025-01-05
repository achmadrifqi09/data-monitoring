import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    // https: true,
    // server: {
    //     proxy: {
    //         '/': 'https://3d29-114-125-94-51.ngrok-free.app',
    //     },
    // },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
