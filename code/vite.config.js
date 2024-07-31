import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: true,
        port: 8000,
        watch: {
            usePolling: true,
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/sass/styles.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: '@import "bootstrap/scss/bootstrap";',
                sassOptions: {
                    quietDeps: true,
                },
            },
        },
    },
});
