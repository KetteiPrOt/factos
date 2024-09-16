import { defineConfig } from 'vite';
import { sveltekit } from '@sveltejs/kit/vite';
// import laravel from 'laravel-vite-plugin';

export default defineConfig({
    // plugins: [
    //     laravel({
    //         input: ['resources/css/app.css', 'resources/js/app.js'],
    //         refresh: true,
    //     }),
    // ],
    plugins: [sveltekit()],
    server: {
        fs: {
            allow: ['..']
        }
    }
});
