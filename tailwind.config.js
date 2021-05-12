const colors = require('tailwindcss/colors');

module.exports = {
  purge: [],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {},
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      black: colors.black,
      white: colors.white,
      gray: colors.trueGray,
      indigo: colors.indigo,
      rose: colors.rose,
      amber: {
            900: '#AF2E11',
            800: '#CE2A04',
            700: '#E33405',
            DEFAULT: '#CF4322',
            400: '#EB9743',
            300: '#F1C420',
            menu: '#070707'
        }
    }
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
