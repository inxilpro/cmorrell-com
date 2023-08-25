<div class="my-12 lg:pl-12 lg:-mr-4 lg:pr-4 lg:-my-4 lg:py-8 lg:opacity-50 hover:opacity-100 transition-opacity lg:h-screen lg:sticky lg:top-0 overflow-y-auto overflow-x-hidden text-base leading-none shadow-overflow-y">
	
	<a 
		href="https://overengineered.fm/" 
		target="_blank" 
		class="block mb-6 mx-auto group text-center"
		style="max-width: 250px"
	>
		<div class="flex flex-col align-center border-2 border-purple-400 rounded-lg overflow-hidden shadow">
			<img
				class="h-auto w-full"
				src="https://media.zencastr.com/image-files/560d77e29388483a4269cc1a/ab9e695e-eac1-4125-9ca1-5764bd441959.png"
				alt="overengineered.fm"
			/>
		</div>
		
		<div class="p-4 text-gray-600 tracking-wider font-bold font-slant">
			Check out our new podcast, <span class="group-hover:underline group-hover:text-blue-800">Over Engineered</span>
		</div>
	</a>
	
	<h2 class="text-3xl font-bold font-slant text-gray-900 mt-0 mb-4 border-t pt-4">
		Interesting…
	</h2>
	
	@foreach($stars as $star)
		<div class="mb-6">
			<h3 class="mb-1">
				<a href="{{ $star['html_url'] }}" target="_blank" rel="noopener noreferrer" class="text-xl tracking-wider font-bold font-slant group">
					<span class="text-gray-600 group-hover:text-blue-700">
						{{ $star['owner']['login'] }}
					</span>
					<span class="text-gray-800 group-hover:text-blue-700 inline-block mx-1">
						/
					</span>
					<span class="text-gray-800 group-hover:text-blue-700 group-hover:underline">
						{{ $star['name'] }}
					</span>
				</a>
			</h3>
			<p class="leading-snug text-base text-gray-900">
				{{ $star['description'] }}
			</p>
		</div>
	@endforeach
	
	<a
		class="block my-12 pt-6 border-t text-center text-2xl tracking-wider font-bold font-slant opacity-75 hover:opacity-100"
		href="https://github.com/inxilpro?tab=stars"
		target="_blank"
		rel="noopener noreferrer"
	>
		More…
	</a>
</div>
