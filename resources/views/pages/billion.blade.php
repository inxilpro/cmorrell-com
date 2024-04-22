@php use App\Http\Downloads; @endphp
<x-layout title="Countdown to One Billion - Chris Morrell">
	<h1 class="text-5xl lg:text-6xl font-bold font-slant text-gray-800">
		Countdown to One Billion.
	</h1>
	
	<p class="text-lg lg:text-xl leading-normal my-4 text-gray-700">
		I've been slowly churning out open source packages for almost two decades. Most of those
		packages have been duds or minor successes, but a few have been <em>“hits”</em>. So when
		<a class="underline hover:text-gray-500" href="https://1billion.spatie.be/" target="_blank">Spatie announced their 1 billionth download</a>,
		I thought it'd be fun to make my own…
	</p>
	
	<p class="text-lg lg:text-xl leading-normal my-4 text-gray-700">
		So without further ado, here's the sum total downloads (updated daily) of all the packages I maintain
		across Packagist (PHP) and NPM (JavaScript):
	</p>
	
	<div class="border border-purple-600 bg-purple-50 rounded-xl p-8 text-center my-12">
		<p class="text-5xl lg:text-6xl font-bold font-slant text-purple-900 tracking-widest">
			{{ number_format(app(\App\Http\Downloads::class)()->total) }}
		</p>
		<p class="font-semibold text-purple-900 italic mt-2 opacity-50">
			Downloads
		</p>
	</div>

</x-layout>
