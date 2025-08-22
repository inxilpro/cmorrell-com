@php use App\Http\Downloads;$downloads = app(Downloads::class)(); @endphp

<x-layout title="Countdown to One Billion - Chris Morrell" og-title="Countdown to One Billion">
	<h1 class="text-5xl lg:text-6xl font-bold font-slant text-gray-800">
		Countdown to One Billion.
	</h1>
	
	<p class="text-lg lg:text-2xl leading-normal my-4 text-gray-700">
		I've been slowly churning out open source packages for almost two decades. Most of those
		packages have been duds or minor successes, but a few have been <em>“hits”</em>. So when
		<a class="text-gray-600 font-bold underline hover:text-black" href="https://1billion.spatie.be/" target="_blank">Spatie announced their 1 billionth download</a>,
		I thought it'd be fun to make my own little countdown…
	</p>
	
	<p class="text-lg lg:text-2xl leading-normal my-4 text-gray-700">
		So without further ado, here's the sum total downloads (updated daily) of all the packages I maintain
		across Packagist (PHP) and NPM (JavaScript):
	</p>
	
	<div class="border border-purple-600 bg-purple-50 rounded-xl p-8 text-center my-12">
		<p class="text-5xl lg:text-6xl font-bold font-slant text-purple-900 tracking-widest">
			{{ number_format($downloads->total) }}
		</p>
		<p class="font-semibold text-purple-900 italic mt-2 opacity-50">
			Downloads
		</p>
	</div>
	
	<div x-data="{ expanded: false }" class="my-24">
		<button @click="expanded = ! expanded" class="text-gray-600 font-bold underline hover:text-black">
			Prove it.
		</button>
		
		<div x-show="expanded" x-collapse>
			<table class="w-full my-6">
				<thead>
					<tr class="bg-gray-50">
						<th class="font-slant text-2xl px-4 py-2 text-left">
							Repository
						</th>
						<th class="font-slant text-2xl px-4 py-2 text-left">
							Package
						</th>
						<th class="font-slant text-2xl px-4 py-2 text-right">
							Downloads
						</th>
					</tr>
				</thead>
				<tbody>
					@foreach($downloads->data as [$repo, $pkg, $count])
						<tr>
							<td class="px-4 py-2">
								{{ $repo }}
							</td>
							<td class="px-4 py-2 font-mono">
								{{ $pkg }}
							</td>
							<td class="px-4 py-2 tabular-nums text-right">
								{{ $count }}
								@if('app-root-path' === $pkg)
									😛
								@endif
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>

</x-layout>
