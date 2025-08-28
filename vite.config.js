import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // Tambahkan category.js ke dalam array input
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/category.js',
            ],
            refresh: true,
        }),
    ],
});
