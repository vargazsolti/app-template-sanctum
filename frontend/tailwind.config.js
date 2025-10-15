/** @type {import('tailwindcss').Config} */
export default {
  darkMode: ['class'],
  content: [
    './index.html',
    './src/**/*.{vue,js,jsx}',
  ],
  theme: { extend: {} },
  plugins: [
    require('@tailwindcss/forms'),
    require('tailwindcss-animate'), // ✅ itt regisztrálod
  ],
}
