import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                ink: '#17312A',
                emerald: '#0F6B58',
                forest: '#083F34',
                mint: '#E7F0EA',
                paper: '#F4F1E8',
                cream: '#FAF8F2',
                brass: '#C9A86A',
                line: 'rgba(23,49,42,.10)',
                'brand-forest': '#083F34',
                'brand-primary': '#0F6B58',
                'brand-accent': '#C9A86A',
                'brand-accent-hover': '#b89456',
                'brand-cream': '#F4F1E8',
                'brand-surface': '#FAF8F2',
                'brand-text': '#17312A',
                'brand-muted': '#52756A',
                'brand-border': 'rgba(23,49,42,.10)',
            },
            boxShadow: {
                float: '0 20px 60px rgba(8,63,52,.12)',
                card: '0 8px 30px rgba(8,63,52,.08)',
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};
