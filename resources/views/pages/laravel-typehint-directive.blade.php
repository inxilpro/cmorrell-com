<x-layout>
	<h1 class="text-5xl lg:text-6xl font-bold font-slant text-gray-800">
		Laravel {{ '@' }}typehint Directive
	</h1>
	
	<p class="text-xl lg:text-2xl leading-normal my-4">
		If you use PhpStorm, you might find yourself adding type annotations to your
		Blade templates from time to time. This directive gives you a more ergonomic option:
	</p>
	
	<x-torchlight-code language="blade">
		@typehint(\App\User $user)
	</x-torchlight-code>
	
	<img src="{{ asset('images/shots/typehint-phpstorm.png') }}" />
	
</x-layout>
