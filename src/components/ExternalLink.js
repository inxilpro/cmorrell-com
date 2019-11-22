/**
 * Layout component that queries for data
 * with Gatsby's useStaticQuery component
 *
 * See: https://www.gatsbyjs.org/docs/use-static-query/
 */

import React from "react";

const defaultClassName = `text-gray-800 font-bold hover:underline`;

export default function ExternalLink({ children, to, className = defaultClassName }) {
	return <a
		target="_blank"
		rel="noopener noreferrer"
		href={ to }
		className={ className }
		children={ children }
	/>;
};
