import defaultTheme from 'tailwindcss/defaultTheme';
const colorsTheme = require('tailwindcss/colors');
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.{vue,js,ts,tsx,jsx,mjs,cjs,blade.php,css,scss}',
        './Modules/*/resources/**/*.{vue,js,ts,tsx,jsx,mjs,cjs,blade.php,css,scss}',
    ],
    darkMode: "class", // or 'media' or 'class'
    theme: {
        extend: {
            colors: {
                gray: colorsTheme.neutral
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};
