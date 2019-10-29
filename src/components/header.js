import { Link } from "gatsby";
import React from "react";
import Github from 'simple-icons/icons/github.svg';
import Twitter from 'simple-icons/icons/twitter.svg';

export default function Header({ siteTitle }) {
	return (
		<header className="bg-gray-900 text-white">
			<div className="container mx-auto p-4 flex items-center">
				<h1 style={ { margin: 0 } }>
					<Link to="/" className="text-white hover:underline">
						{ siteTitle }
					</Link>
				</h1>
				<div className="ml-auto flex -mx-1">
					<a className="mx-1 no-underline opacity-75 hover:opacity-100"
					   href="https://twitter.com/inxilpro"
					   target="_blank"
					   rel="noopener noreferrer"
					   title="Chris Morrell on Twitter"
					   children={ <Twitter className="h-6 w-6 fill-current" /> } />
					<a className="mx-1 no-underline opacity-75 hover:opacity-100"
					   href="https://github.com/inxilpro/"
					   target="_blank"
					   rel="noopener noreferrer"
					   title="Chris Morrell on Github"
					   children={ <Github className="h-6 w-6 fill-current" /> } />
				</div>
			</div>
		</header>
	);
}
