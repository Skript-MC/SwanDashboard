/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html.twig',
    './assets/vue/**/*.vue',
  ],
  theme: {
    fontFamily: {
      sans: ['Raleway', 'sans-serif'],
    },
    extend: {
        colors: {
            'sky': '#2d98da',
        },
        height: {
            18: '4.5rem',
        }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
