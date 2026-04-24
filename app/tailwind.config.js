import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    safelist: [
        'text-ink',
        'text-sand',
        'text-foam',
    ],

    theme: {
        extend: {
            colors: {
                ink: {
                    DEFAULT: '#0f2d5c',
                    2: '#1e40af',
                    soft: '#3c5a84',
                    line: 'rgba(15,45,92,0.15)',
                },
                sun: {
                    DEFAULT: '#d89b2a',
                    deep: '#a8751a',
                },
                coral: {
                    DEFAULT: '#c85a3c',
                    soft: '#e28566',
                },
                sand: {
                    DEFAULT: '#faf3e3',
                    2: '#f1e5c9',
                    3: '#e8d7a9',
                },
                foam: '#ffffff',
                seaweed: '#2d4a3e',
            },
            fontFamily: {
                display: ['Fraunces', ...defaultTheme.fontFamily.serif],
                sans:    ['Instrument Sans', ...defaultTheme.fontFamily.sans],
                mono:    ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            letterSpacing: {
                tightest: '-0.035em',
                mono:     '0.18em',
            },
            boxShadow: {
                card: '0 1px 2px rgba(15,45,92,0.06), 0 20px 40px -20px rgba(15,45,92,0.18)',
                lift: '0 30px 60px -30px rgba(15,45,92,0.35)',
            },
            animation: {
                rise: 'rise 0.8s cubic-bezier(0.16, 1, 0.3, 1) backwards',
            },
            keyframes: {
                rise: {
                    from: { opacity: '0', transform: 'translateY(28px)' },
                    to:   { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms],
};
