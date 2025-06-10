import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',

                // Les assets du templates
                'resources/assets/borex/css/app.css',
                'resources/assets/borex/js/app.js',
            ],
            refresh: true,
        }),
    ],

    build:{
        rollupOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.includes('borex')) {
                        return 'assets/borex/[name].[hash][extname]';
                    }
                    return 'assets/[name].[hash][extname]';
                }
            },
        }
    }
});
