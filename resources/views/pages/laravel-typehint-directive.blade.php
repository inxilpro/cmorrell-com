<x-layout title="Laravel @typehint Directive - Chris Morrell">
	<h1 class="text-5xl lg:text-6xl font-bold font-slant text-gray-800">
		Laravel {{ '@' }}typehint Directive
	</h1>
	
	<div class="bg-blue-100 p-4 border border-blue-200 rounded my-4 flex items-center">
		<div class="w-10">
			<svg class="w-6 h-6 text-blue-800 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
				<path d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 448c-110.532 0-200-89.431-200-200 0-110.495 89.472-200 200-200 110.491 0 200 89.471 200 200 0 110.53-89.431 200-200 200zm0-338c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z" />
			</svg>
		</div>
		<div class="flex-1">
			It's worth noting that <a href="https://laravel-idea.com/" target="_blank">Laravel IDEA</a>
			makes 90% of type annotations obsolete. If you're not using Laravel IDEA, <strong>go buy
				it now!</strong> For the other 10%, this trick helps!
		</div>
	</div>
	
	<p class="text-xl lg:text-2xl leading-normal my-4">
		If you use PhpStorm, you might find yourself adding type annotations to your
		Blade templates from time to time. This directive gives you a more ergonomic option:
	</p>
	
	<pre>
		<x-torchlight-code language="blade" class="p-4">@verbatim
			{{-- Now my IDE knows that $user is a User object --}}
			<?php echo '<?php'; ?> /** @var \App\User $user **/ <?php echo '?>'; ?> {{-- [tl! --] --}}
			@typehint(\App\User $user) {{-- [tl! ++] --}}
		@endverbatim</x-torchlight-code>
	</pre>
	
	<p class="text-xl lg:text-2xl leading-normal my-4">
		First, open up the PhpStorm blade settings and add this:
	</p>
	
	<img src="{{ asset('images/shots/typehint-phpstorm.png') }}" />
	
	<p class="text-lg lg:text-xl leading-normal my-4 text-gray-700 italic">
		(The <code>null|</code> portion is unfortunately necessary due to how PhpStorm 
		parses custom Blade configs.)
	</p>
	
	<p class="text-xl lg:text-2xl leading-normal my-4">
		Then register an empty directive in your service provider. Note that the directive
		doesn't have to actually do anything, because it's just there to support code
		intelligence in the IDE.
	</p>
	
	<pre>
		<x-torchlight-code language="php" class="p-4">
			Blade::directive('typehint', static fn() => '');
		</x-torchlight-code>
	</pre>
	
	<p class="text-xl lg:text-2xl leading-normal my-4">
		Now you can swap out clunky phpdoc annotations with slightly more 
		elegant {{ '@' }}typehint directive!
	</p>

</x-layout>
