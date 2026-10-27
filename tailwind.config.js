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
                sans: ['ui-rounded', 'Nunito', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                clay: {
                    canvas: '#f4eee9',
                    surface: '#fffaf6',
                    peach: '#fbe3d5',
                    pink: '#f7b8cf',
                    blue: '#dcecf9',
                    mint: '#dceee3',
                    yellow: '#f9e7aa',
                    ink: '#352d40',
                },
            },
            borderRadius: {
                clay: '2rem',
            },
            boxShadow: {
                clay: '9px 12px 24px #bca99b40, -5px -5px 14px #ffffffb3, inset 3px 3px 6px #ffffff, inset -5px -6px 10px #bca99b26',
                'clay-small': '4px 5px 10px #a58c7b33, inset 2px 2px 4px #ffffffb3, inset -3px -4px 6px #70584d1f',
                'clay-pink': '4px 6px 0 #d98ba8, 6px 10px 16px #b86b8a2b, inset 3px 3px 5px #ffffff99, inset -3px -4px 6px #c5658b40',
                'clay-pressed': '1px 2px 0 #d98ba8, inset 2px 3px 6px #b65a8140, inset -2px -2px 4px #ffffff66',
                'clay-inset': 'inset 3px 3px 7px #bca99b30, inset -3px -3px 6px #ffffff',
            },
        },
    },
    plugins: [forms],
};
