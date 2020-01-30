import React from "react";
import { Helmet } from "react-helmet";
import Layout from "../components/layout";

// TODO: Source https://api.npmjs.org/downloads/point/2000-01-01:2019-10-28/app-root-path

export default function FinancialAdvice() {
	return (
		<Layout>
			
			<Helmet>
				<meta name="robots" content="noindex" />
			</Helmet>
			
			<h1 className="text-5xl lg:text-6xl font-bold font-slant text-gray-800">
				Some totally unsubstantiated advice…
			</h1>
			
			<p className="text-xl lg:text-2xl leading-normal my-4">
				Coming soon!
			</p>
		
		</Layout>
	);
}
