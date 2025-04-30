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
        },
        colors: {
            "primary-grey" : "#0F2033",
            "secondary-grey" : "#F5F5F5",
            "off-white" : "#F9FBFF",
            "blue-accent" : "#678BD8",
            "blue-hover" : "#5c7dc4",
            "red-accent" : "#DB7171",
            "red-hover" : "#bf6363",
        }
    },

    plugins: [forms],
};
