import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/blog-editor.js',
                'resources/js/blog-management.js',
                'resources/js/theme.js',
                'resources/css/blog-editor.css',
                'resources/css/blog-management.css',
                'resources/css/blog-home.css',
                'resources/css/blog-pages.css',
                'resources/css/site.css',
                'resources/js/site.js',
                'resources/js/blog-home.js',
                'resources/js/blog-pages.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
