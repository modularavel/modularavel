import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import collectModuleAssetsPaths from './vite-module-loader';

async function getConfig() {
    const paths = [
        'resources/css/app.css',
        'resources/js/app.js',
    ];

    const allPaths = await collectModuleAssetsPaths(paths, 'Modules');

    return defineConfig({
        plugins: [
            laravel({
                input: allPaths,
                refresh: {
                    paths: [
                        `app/**`,
                        `lang/**`,
                        `config/**`,
                        `routes/**`,
                        `resources/views/**`,

                        // Refresh modules path
                        `Modules/*/src/**`,
                        `Modules/*/lang/**`,
                        `Modules/*/config/**`,
                        `Modules/*/routes/**`,
                        `Modules/*/resources/views/**`,
                    ],
                    config: {
                        always: false
                    }
                },
            }),
            tailwindcss(),
        ],
        server: {
            cors: true,
        },
    });
}

export default getConfig();
