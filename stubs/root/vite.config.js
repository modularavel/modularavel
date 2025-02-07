import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import collectModuleAssetsPaths from './vite-module-loader';

async function getConfig() {
    const paths = [
        'resources/css/app.css',
        'resources/js/app.js',
    ];

    const allPaths = await collectModuleAssetsPaths(paths, './Modules');

    return defineConfig({
        plugins: [
            laravel({
                input: allPaths,
                // refresh: true
                refresh: {
                    paths: [
                        './app/**',
                        './config/**',
                        './lang/*',
                        './Modules/**'
                    ],
                    config: {
                        always: false,
                        log: true
                    }
                },
            })
        ]
    });
}

export default getConfig();
