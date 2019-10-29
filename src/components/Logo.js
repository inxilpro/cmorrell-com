import React from "react";
import { graphql, useStaticQuery } from "gatsby";
import Img from "gatsby-image";

export default function Logo() {
	const data = useStaticQuery(graphql`
        query {
            placeholderImage: file(relativePath: { eq: "cm-icon.png" }) {
                childImageSharp {
                    fluid(maxWidth: 512) {
                        ...GatsbyImageSharpFluid
                    }
                }
            }
        }
	`);
	
	return <Img
		fluid={ data.placeholderImage.childImageSharp.fluid }
		style={ { width: 128, height: 128 } }
	/>;
};
