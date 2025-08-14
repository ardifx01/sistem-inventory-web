import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class', // penting
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
                background: {
                    light: '#f9fafb', // light mode background
                    dark: '#1f2937',  // dark mode background
                },
                surface: {
                    light: '#ffffff',
                    dark: '#111827',
                },
                border: {
                    light: '#d1d5db',
                    dark: '#374151',
                },
                text: {
                    light: '#1f2937',
                    dark: '#e5e7eb',
                }
            }
        },
    },

    plugins: [forms],
};
