import React from 'react';
import { MDXProvider } from "@mdx-js/react"

const components = {
	h1: props => <h1 className="text-5xl lg:text-6xl font-bold font-slant text-gray-800" { ...props } />,
	h2: props => <h2 className="text-xl lg:text-3xl font-bold font-slant my-4" { ...props } />,
	h3: props => <h3 className="text-xl lg:text-3xl font-bold font-slant mt-6 mb-2 text-gray-600" { ...props } />,
	h4: props => <h4 className="text-xl lg:text-2xl font-bold mt-6 mb-1" { ...props } />,
	p: props => <p className="text-xl lg:text-2xl leading-normal mb-4" { ...props } />,
	ul: props => <ul className="pl-12 my-4 list-disc" {...props} />,
	ol: props => <ul className="pl-12 my-4 list-decimal" {...props} />,
	li: props => <li className="text-xl lg:text-2xl leading-normal mb-4" {...props} />,
};

export default function MarkdownLayout({ children }) {
	return (
		<MDXProvider components={ components }>
			{ children }
		</MDXProvider>
	)
}
