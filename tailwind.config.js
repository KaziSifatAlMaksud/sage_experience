import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                sage: {
                    50: 'rgb(240 247 240)',
                    100: 'rgb(220 235 220)',
                    200: 'rgb(190 215 190)',
                    300: 'rgb(150 189 150)',
                    400: 'rgb(110 156 110)',
                    500: 'rgb(80 129 80)',
                    600: 'rgb(60 101 60)',
                    700: 'rgb(50 81 50)',
                    800: 'rgb(40 66 40)',
                    900: 'rgb(30 56 30)',
                    950: 'rgb(20 30 20)'
                }
            },
        },
    },

    plugins: [forms],
};
