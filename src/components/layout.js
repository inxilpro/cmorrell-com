/**
 * Layout component that queries for data
 * with Gatsby's useStaticQuery component
 *
 * See: https://www.gatsbyjs.org/docs/use-static-query/
 */

import React from "react";
import { graphql, useStaticQuery } from "gatsby";

import "../app.css";

import Header from "./header";

export default function Layout({ children }) {
	const data = useStaticQuery(graphql`
        query SiteTitleQuery {
            site {
                siteMetadata {
                    title
                }
            }
        }
	`);
	
	return (
		<div className="flex flex-col min-h-screen antialiased">
			<Header siteTitle={ data.site.siteMetadata.title } />
			<main className="flex-1">
				<div className="container mx-auto p-4">
					{ children }
				</div>
			</main>
			<footer className="bg-gray-100">
				<div className="container mx-auto p-4 py-8 pb-12 text-sm text-gray-700">
					&copy; { new Date().getFullYear() } Chris Morrell
				</div>
			</footer>
		</div>
	);
};
