<x-layout>
	<h1 class="text-5xl lg:text-6xl font-bold font-slant text-gray-800">
		Laravel {{ '@' }}typehint Directive
	</h1>
	
	<p class="text-xl lg:text-2xl leading-normal my-4">
		If you use PhpStorm, you might find yourself adding type annotations to your
		Blade templates from time to time. This directive gives you a more ergonomic option:
	</p>
	
	<pre>
		<x-torchlight-code language="blade">
			@verbatim
			{{-- Now my IDE knows that $user is a User object --}}
			@typehint(\App\User $user)
			@endverbatim
		</x-torchlight-code>
	</pre>
	
	<p class="text-xl lg:text-2xl leading-normal my-4">
		First, open up the PhpStorm blade settings and add this:
	</p>
	
	<img src="{{ asset('images/shots/typehint-phpstorm.png') }}" />
	
	<p class="text-xl lg:text-2xl leading-normal my-4">
		Then register an empty directive in your service provider:
	</p>
	
	<pre>
		<x-torchlight-code language="php">
			Blade::directive('typehint', static fn() => '');
		</x-torchlight-code>
	</pre>

</x-layout>
