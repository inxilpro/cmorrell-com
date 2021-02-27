import React, { useState } from "react";
import Layout from "../components/layout";
import SEO from '../components/seo.js';
import Article from '../mdx/laravel-relationships.mdx';

const Comment = ({ children }) => <div
	className="text-gray-600 font-mono whitespace-no-wrap"
	children={ children }
/>;

const ConfigLine = ({ children }) => <div
	className="text-gray-900 font-mono whitespace-no-wrap"
	children={ children }
/>;

const Spacer = () => <div className="my-4" />;

export default function LaravelRelationships() {
	
	return (
		<Layout>
			
			<SEO title="Digging into Laravel relationships - Chris Morrell" />
			
			<Article />
		
		</Layout>
	);
}
