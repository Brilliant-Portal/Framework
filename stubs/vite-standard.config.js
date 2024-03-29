import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

let host = 'localhost.test';

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/js/app.js"],
            refresh: true,
            detectTls: host,
        }),
    ],
});
