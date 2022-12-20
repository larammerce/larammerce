import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

import vue from "@vitejs/plugin-vue";

export default defineConfig({
    "plugins": [
        laravel({
            "input": ['resources/assets/css/app.css', 'resources/assets/js/app.js'],
            "refresh": true,
        }),
        vue(),
    ],
});
