import React from "react";
import { graphql, useStaticQuery } from "gatsby";

export default function Sidebar() {
	const data = useStaticQuery(graphql`
        query StarredRepositoriesQuery {
            allGithubData {
                nodes {
                    data {
                        viewer {
                            starredRepositories {
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
                    }
                }
            }
        }
	`);
	
	const repos = data.allGithubData.nodes[0].data.viewer.starredRepositories.nodes;
	
	return (
		<div className="lg:pl-12 lg:-mr-4 lg:pr-4 lg:-my-4 lg:py-8 lg:opacity-50 hover:opacity-100 transition transition-opacity h-screen sticky top-0 overflow-y-auto overflow-x-hidden text-base leading-none shadow-overflow-y">
			<h2 className="text-3xl font-bold font-slant text-gray-900 mb-8">
				Interesting&hellip;
			</h2>
			
			{ repos.map(({ shortDescriptionHTML, name, owner, url, id }) => (
				<div key={ id } className="mb-4">
					<h3 className="mb-1">
						<a href={url} target="_blank" rel="noopener noreferrer" className="text-xl tracking-wider font-bold font-slant group">
							<span className="text-gray-600 group-hover:text-blue-700">
								{ owner.login }
							</span>
							<span className="text-gray-800 group-hover:text-blue-700 inline-block mx-1">
								/
							</span>
							<span className="text-gray-800 group-hover:text-blue-700 group-hover:underline">
								{ name }
							</span>
						</a>
					</h3>
					<p className="leading-snug text-base text-gray-900"
					   dangerouslySetInnerHTML={ { __html: shortDescriptionHTML } } />
				</div>
			)) }
		</div>
	);
}
