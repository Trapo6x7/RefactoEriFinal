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
            "off-white" : "#E5EDFF",
            "blue-accent" : "#678BD8",
            "red-accent" : "#DB7171",
        }
    },

    plugins: [forms],
};
