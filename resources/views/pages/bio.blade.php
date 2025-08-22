<x-layout>
	
	<div class="flex justify-start items-center gap-6 mt-12">
		<video class="w-full h-auto max-w-xs rounded-lg aspect-9/16 object-cover hidden sm:block" autoplay loop muted playsinline>
			<source src="{{ asset('images/cm.mp4') }}" type="video/mp4" />
		</video>
		<div>
			<h1 class="text-5xl lg:text-6xl font-bold font-slant text-gray-800">
				Chris Morrell
			</h1>
			
			<p class="text-xl lg:text-2xl leading-normal my-4">
				Chris is a father, the CEO and CTO of the International Association of Certified Home
				Inspectors (InterNACHI), and an avid open-source contributor. He's been building
				tools to help home inspectors for 25+ years, and tries to contribute as much
				of his work back to the open source community as possible.
			</p>
			
			<div class="mt-12">
				<a
					href="{{ asset('images/headshot.zip') }}"
					class="inline-flex items-center gap-x-2 rounded-md bg-gray-100 px-3.5 py-2.5 text-2xl font-slant text-black shadow-xs hover:bg-gray-200 focus-visible:outline-solid focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
					download
				>
					<img
						class="w-12 h-12 rounded-full"
						alt="Chris Morrell"
						src="{{ asset('images/cm-headshot-for-web.jpg') }}"
					/>
					Download Headshot
				</a>
			</div>
		
		</div>
	</div>

</x-layout>
