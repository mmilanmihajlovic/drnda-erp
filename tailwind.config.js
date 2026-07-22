/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                sidebar: { DEFAULT: '#1a1f2e', hover: '#232840', border: '#2a3045', text: '#8a94b0', active: '#c9a84c' },
                gold: { DEFAULT: '#c9a84c', light: '#e0c878', dark: '#8a6e30' },
            },
        },
    },
    plugins: [],
};
