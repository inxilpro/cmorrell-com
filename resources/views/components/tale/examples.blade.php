@props([
	'traditional',
	'eventSourced',
])

<div class="p-4 max-w-[1400px] mx-auto lg:border lg:rounded">
	<x-columns>
		<x-columns.column>
			<x-markdown :file="$traditional" />
		</x-columns.column>
		<x-columns.column>
			<x-markdown :file="$eventSourced" />
			{{ $afterEventSourced ?? null }}
		</x-columns.column>
	</x-columns>
</div>
