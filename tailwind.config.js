/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  safelist: [
    {
      pattern: /text-(green|red|yellow|blue|gray)-700/,
      variants: ['hover'],
    },
    {
      pattern: /bg-(green|red|yellow|blue|gray)-100/,
    },
    {
      pattern: /border-(green|red|yellow|blue|gray)-500/,
    }
  ],
  theme: {
    extend: {},
    screens: {
      sm: '640px',
      md: '768px',
      lg: '1024px',
      xl: '1280px',
      '2xl': '1536px',
    },
  },
  plugins: [],
}
