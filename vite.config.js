import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({

    // 1. Add this entire server block
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        cors: true,
        origin: 'https://bug-free-space-guide-j97j44v569j2qjqg-5173.app.github.dev',
        hmr: {
            host: 'bug-free-space-guide-j97j44v569j2qjqg-5173.app.github.dev',
            clientPort: 443,
            protocol: 'wss'
        }
    },

    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
