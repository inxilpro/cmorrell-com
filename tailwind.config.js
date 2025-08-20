module.exports = {
	content: [
		"./resources/**/*.blade.php",
		"./app/View/Components/**/*.php",
		"./app/Support/MarkdownConverter.php",
		"./resources/**/*.js",
	],
	theme: {
		extend: {},
		screens: {
			'sm': '640px',
			'lg': '960px',
			'xl': '1080px',
		},
	},
	variants: {
		textColor: ['responsive', 'hover', 'focus', 'group-hover'],
		textDecoration: ['responsive', 'hover', 'focus', 'group-hover'],
	},
	plugins: [],
};
