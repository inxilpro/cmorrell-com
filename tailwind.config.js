module.exports = {
	content: [
		"./resources/**/*.blade.php",
		"./resources/**/*.js",
	],
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
	plugins: [],
};
