require(`dotenv`).config();

module.exports = {
	siteMetadata: {
		title: `Chris Morrell`,
		description: `The personal website of Chris Morrell.`,
		author: `@inxilpro`,
	},
	plugins: [
		{
			resolve: `gatsby-source-github-api`,
			options: {
				token: process.env.GITHUB_TOKEN,
				graphQLQuery: `query { 
				  user(login:"inxilpro") {  
				    starredRepositories (first: 50, orderBy: { field:STARRED_AT, direction:DESC }) {
				      nodes {
				        id,
				        url,
				        name,
				        owner {
				          login,
				        },
				        shortDescriptionHTML
				      }
				    }
				  }
				}`,
			}
		},
		`gatsby-plugin-react-helmet`,
		{
			resolve: `gatsby-plugin-postcss`,
			options: {
				postCssPlugins: [
					require('tailwindcss'),
				],
			},
		},
		{
			resolve: `gatsby-source-filesystem`,
			options: {
				name: `images`,
				path: `${ __dirname }/src/images`,
			},
		},
		`gatsby-transformer-sharp`,
		`gatsby-plugin-sharp`,
		{
			resolve: `gatsby-plugin-manifest`,
			options: {
				name: `gatsby-starter-default`,
				short_name: `starter`,
				start_url: `/`,
				background_color: `#424957`,
				theme_color: `#424957`,
				display: `minimal-ui`,
				icon: `src/images/cm-icon.png`, // This path is relative to the root of the site.
			},
		},
		{
			resolve: `gatsby-plugin-react-svg`,
			options: {
				rule: {
					include: /simple-icons/
				},
			},
		},
		{
			resolve: `gatsby-plugin-purgecss`,
			options: {
				printRejected: false,
				tailwind: true,
				whitelist: [], // Don't remove these selectors
			}
		},
		{
			resolve: `gatsby-plugin-mdx`,
			options: {
				defaultLayouts: {
					default: require.resolve('./src/components/MarkdownLayout.js'),
				}
			},
		},
		// this (optional) plugin enables Progressive Web App + Offline functionality
		// To learn more, visit: https://gatsby.dev/offline
		// `gatsby-plugin-offline`,
	],
};
