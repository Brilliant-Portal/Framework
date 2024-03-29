import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import livewire from '@defstudio/vite-livewire-plugin';

let host = 'localhost.test';

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
            detectTls: host,
        }),

        livewire({
            refresh: ["resources/css/app.css"],
        }),
    ],
});
