<div class="my-12 lg:pl-12 lg:-mr-4 lg:pr-4 lg:-my-4 lg:py-8 lg:opacity-50 hover:opacity-100 transition-opacity lg:h-screen lg:sticky lg:top-0 overflow-y-auto overflow-x-hidden text-base leading-none shadow-overflow-y">
	<h2 class="text-3xl font-bold font-slant text-gray-900 mb-8">
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
