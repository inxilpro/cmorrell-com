<x-layout>
	<div class="lg:flex">
		<div class="flex-grow">
			
			<h1 class="text-5xl lg:text-6xl font-bold font-slant text-gray-800">
				Hi, I'm Chris.
			</h1>
			
			<p class="text-xl lg:text-2xl leading-normal my-4">
				By day, I work at the <a href="https://www.nachi.org/">International Association
					of Certified Home Inspectors</a> (InterNACHI) as both the Chief Executive Officer
				and Chief Technology Officer. InterNACHI is a professional association for home inspectors
				that focuses on using technology to deliver the best education, testing, and certification
				available to the home inspection industry.
			</p>
			
			<br />
			
			<p class="text-xl leading-normal my-4">
				While the vast majority of my time is dedicated to InterNACHI (and helping other organizations
				like it), I'm a programmer at heart and still contribute frequently to open source projects.
			</p>
			
			<div class="flex my-8 p-4 border rounded-lg items-center">
				<a class="mr-4 no-underline opacity-75 hover:opacity-100"
				   href="https://github.com/inxilpro/"
				   target="_blank"
				   rel="noopener noreferrer"
				   title="Chris Morrell on Github"
				>
					<svg class="h-8 lg:h-12 w-8 lg:w-12 fill-current" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>GitHub</title>
						<path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12" />
					</svg>
				</a>
				<a class="font-slant text-xl lg:text-3xl leading-none opacity-75 hover:opacity-100"
				   href="https://github.com/inxilpro/"
				   target="_blank"
				   rel="noopener noreferrer">
					See my latest projects on GitHub
				</a>
			</div>
			
			<br />
			
			<p class="text-xl leading-normal my-4">
				And as much as I dislike the way that Twitter enables harassment and bigotry
				on the internet, I've carved out my own safe haven of folks who treat each other
				with respect and talk about interesting things online:
			</p>
			
			<div class="flex my-8 p-4 border rounded-lg items-center">
				<a class="mr-4 no-underline opacity-75 hover:opacity-100"
				   href="https://twitter.com/inxilpro"
				   target="_blank"
				   rel="noopener noreferrer"
				   title="Chris Morrell on Twitter"
				>
					<svg class="h-8 lg:h-12 w-8 lg:w-12 fill-current" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Twitter</title>
						<path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
					</svg>
				</a>
				<a class="font-slant text-xl lg:text-3xl leading-none opacity-75 hover:opacity-100"
				   href="https://twitter.com/inxilpro"
				   target="_blank"
				   rel="noopener noreferrer">
					Follow me on Twitter
				</a>
			</div>
			
			<br />
			
			<p class="text-xl leading-normal my-4">
				I update this site very infrequently, but I may post interesting things
				here from time-to-time.
			</p>
			
			<ul class="list-disc ml-6">
				<li class="my-2">
					<a href="{{ url('/php-fpm') }}" class="text-gray-600 font-bold underline hover:text-black">
						Tuning dynamic php-fpm settings
					</a> (published in early 2020)
				</li>
			</ul>
		</div>
		{{--
		<Sidebar />
		--}}
	</div>
</x-layout>
