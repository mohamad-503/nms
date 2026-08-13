/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: ['./resources/**/*.blade.php', './resources/js/**/*.{vue,js}', './app/Http/Controllers/**/*.php'],
  theme: {
    extend: {
      fontFamily: { sans: ['Cairo', 'sans-serif'] },
      colors: {
        brand: {
          50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa', 300: '#fdba74',
          400: '#fb923c', 500: '#f97316', 600: '#ea580c', 700: '#c2410c',
          800: '#9a3412', 900: '#7c2d12',
        },
      },
      boxShadow: {
        soft: '0 2px 12px -2px rgba(0,0,0,0.08)',
        card: '0 4px 24px -6px rgba(0,0,0,0.10)',
      },
      borderRadius: { xl: '0.875rem', '2xl': '1.25rem' },
    },
  },
  plugins: [],
}
