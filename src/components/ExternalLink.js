/**
 * Layout component that queries for data
 * with Gatsby's useStaticQuery component
 *
 * See: https://www.gatsbyjs.org/docs/use-static-query/
 */

import React from "react";
import { Link } from "gatsby";

const defaultClassName = `text-gray-800 font-bold hover:underline`;

export default function ExternalLink({ children, to, className = defaultClassName }) {
	return <Link
		target="_blank"
		rel="noopener"
		to={ to }
		className={ className }
		children={ children }
	/>;
};
