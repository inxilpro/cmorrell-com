import React from "react";

import Layout from "../components/layout";
import SEO from "../components/seo";
import ExternalLink from '../components/ExternalLink';
import Github from 'simple-icons/icons/github.svg';
import Twitter from 'simple-icons/icons/twitter.svg';

// TODO: Source https://api.npmjs.org/downloads/point/2000-01-01:2019-10-28/app-root-path

const IndexPage = () => (
	<Layout>
		<SEO title="Chris Morrell" />
		
		<h1 className="text-5xl lg:text-6xl font-bold font-slant text-gray-800">
			Hi, I'm Chris.
		</h1>
		
		<p className="text-xl lg:text-2xl leading-normal my-4">
			By day, I work at the <ExternalLink to="https://www.nachi.org/">International Association
			of Certified Home Inspectors</ExternalLink> (InterNACHI) as both the Chief Executive Officer 
			and Chief Technology Officer. InterNACHI is a professional association for home inspectors
			that focuses on using technology to deliver the best education, testing, and certification
			available to the home inspection industry.
		</p>
		
		<br />
		
		<p className="text-xl leading-normal my-4">
			While the vast majority of my time is dedicated to InterNACHI (and helping other organizations
			like it), I'm a programmer at heart and still contribute frequently to open source projects.
		</p>
		
		<div className="flex my-8 p-4 border rounded-lg items-center">
			<a className="mr-4 no-underline opacity-75 hover:opacity-100"
			   href="https://github.com/inxilpro/"
			   target="_blank"
			   rel="noopener"
			   title="Chris Morrell on Github"
			   children={ <Github className="h-8 lg:h-12 w-8 lg:w-12 fill-current" /> } />
			<a className="font-slant text-xl lg:text-3xl leading-none opacity-75 hover:opacity-100" href="https://github.com/inxilpro/" target="_blank" rel="noopener">
				See my latest projects on GitHub
			</a>
		</div>
		
		<br />
		
		<p className="text-xl leading-normal my-4">
			And as much as I dislike the way that Twitter enables harassment and bigotry
			on the internet, I've carved out my own safe haven of folks who treat each other
			with respect and talk about interesting things online:
		</p>
		
		<div className="flex my-8 p-4 border rounded-lg items-center">
			<a className="mr-4 no-underline opacity-75 hover:opacity-100"
			   href="https://twitter.com/inxilpro"
			   target="_blank"
			   rel="noopener"
			   title="Chris Morrell on Twitter"
			   children={ <Twitter className="h-8 lg:h-12 w-8 lg:w-12 fill-current" /> } />
			<a className="font-slant text-xl lg:text-3xl leading-none opacity-75 hover:opacity-100" href="https://github.com/inxilpro/" target="_blank" rel="noopener">
				Follow me on Twitter
			</a>
		</div>
		
		<br />
		
		<p className="text-xl leading-normal my-4">
			I update this site very infrequently (last update was in { new Date().toLocaleDateString(undefined,{ year: 'numeric', month: 'long' }) }),
			but I may post interesting information here from time-to-time.
		</p>
	
	</Layout>
);

export default IndexPage;
