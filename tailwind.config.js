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
  },
  plugins: [],
}
