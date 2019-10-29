module.exports = {
	theme: {
		extend: {},
		screens: {
			'sm': '640px',
			'lg': '960px',
		},
	},
	variants: {
		textColor: ['responsive', 'hover', 'focus', 'group-hover'],
		textDecoration: ['responsive', 'hover', 'focus', 'group-hover'],
	},
	plugins: [
		require('glhd-tailwindcss-transitions')(),
	],
};
