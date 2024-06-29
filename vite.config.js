import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/notification_Toasts.js',
                'resources/js/bootstrap.js',
                'resources/js/homepage.js',
                'resources/js/meating_room.js',
                'resources/js/auth/register.js',
                'resources/js/auth/profile.js',
                'resources/js/auth/ban_mod_list.js',
                'resources/js/auth/phone_verification.js',
                'resources/js/python/voice_to_speach_form.js',
                'resources/js/python/reduce_noise.js',
                'resources/js/python/convert_file.js',
            ],
            refresh: true,
        }),
    ],
});
