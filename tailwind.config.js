import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                border: 'hsl(214.3 31.8% 91.4%)',
                input: 'hsl(214.3 31.8% 91.4%)',
                ring: 'hsl(222.2 84% 4.9%)',
                background: 'hsl(0 0% 100%)',
                foreground: 'hsl(222.2 84% 4.9%)',
                primary: {
                    DEFAULT: '#0f172a', // Slate 900
                    foreground: '#ffffff',
                },
                secondary: {
                    DEFAULT: 'hsl(210 40% 96.1%)',
                    foreground: 'hsl(222.2 47.4% 11.2%)',
                },
                destructive: {
                    DEFAULT: 'hsl(0 84.2% 60.2%)',
                    foreground: 'hsl(210 40% 98%)',
                },
                muted: {
                    DEFAULT: 'hsl(210 40% 96.1%)',
                    foreground: 'hsl(215.4 16.3% 46.9%)',
                },
                accent: {
                    DEFAULT: 'hsl(210 40% 96.1%)',
                    foreground: 'hsl(222.2 47.4% 11.2%)',
                },
                popover: {
                    DEFAULT: 'hsl(0 0% 100%)',
                    foreground: 'hsl(222.2 84% 4.9%)',
                },
                card: {
                    DEFAULT: 'hsl(0 0% 100%)',
                    foreground: 'hsl(222.2 84% 4.9%)',
                },
            },
            borderRadius: {
                lg: 'var(--radius)',
                md: 'calc(var(--radius) - 2px)',
                sm: 'calc(var(--radius) - 4px)',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'soft': '0 2px 15px -3px rgba(0,0,0,0.07), 0 10px 20px -2px rgba(0,0,0,0.04)',
                'premium': '0 20px 50px -12px rgba(15, 23, 42, 0.12)',
            }
        },
    },

    plugins: [forms],
};
