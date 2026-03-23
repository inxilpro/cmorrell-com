@php
	$versions = Cache::remember('laravel-versions:1', now()->addDay(), function() {
		return Http::createPendingRequest()
			->retry(3, 150)
			->get('https://laravelversions.com/api/versions')
			->json('data');
	});
@endphp

<x-layout title="Laravel Matrix Generator - Chris Morrell">
	
	<h1 class="text-5xl lg:text-6xl font-bold font-slant text-gray-800">
		Laravel Matrix Generator
	</h1>
	
	<p class="text-xl lg:text-2xl leading-normal my-4">
		I build a <a class="text-gray-600 font-bold underline hover:text-black" href="{{ url('/one-billion') }}">lot of open source Laravel projects</a>, and I'm constantly
		needing to tweak my Github Actions test matrix to account for the versions on PHP and Laravel that I support.
	</p>
	
	<p class="text-xl lg:text-2xl leading-normal my-4">
		I got annoyed, and wrote myself a little helper. Maybe it'll help you, too!
	</p>
	
	<div class="border rounded-lg p-4 my-12 bg-white shadow-xl">
		<div x-data="matrix(@js($versions))">
			<ul class="flex gap-1 mb-4">
				<template x-for="target_mode in modes">
					<li>
						<button
							x-text="'supported' === target_mode ? 'Supported' : `v${ target_mode }+`"
							type="button"
							@click="mode = target_mode"
							:class="{
							'rounded text-white px-2 py-0.5 whitespace-nowrap': true,
							'bg-gray-500 cursor-pointer': mode !== target_mode,
							'bg-black cursor-default': mode === target_mode,
						}"
						/>
					</li>
				</template>
			</ul>
			<ul class="flex gap-1 mb-4">
				<template x-for="target_php in php_modes">
					<li>
						<button
							x-text="'all' === target_php ? 'All Versions' : `v${ target_php }+`"
							type="button"
							@click="php_mode = target_php"
							:class="{
								'rounded text-white px-2 py-0.5 whitespace-nowrap': true,
								'bg-gray-500 cursor-pointer': php_mode !== target_php,
								'bg-black cursor-default': php_mode === target_php,
							}"
						/>
					</li>
				</template>
			</ul>
			<pre><code x-text="yml"></code></pre>
		</div>
	</div>
	
	<p class="text-xl lg:text-2xl leading-normal my-4">
		Thanks to the <a class="text-gray-600 font-bold underline hover:text-black" href="https://laravelversions.com" target="_blank">Laravel Versions</a>
		project for the underlying data!
	</p>
	
	<script>
	document.addEventListener('alpine:init', () => {
		const now = new Date();
		Alpine.data('matrix', (versions) => ({
			mode: 'supported',
			php_mode: 'all',
			versions: versions.map(v => ({
				...v,
				supported_php: v.supported_php.filter(v => '' !== v && '?' !== v.at(-1)).map(v => parseFloat(v)),
				released_at: new Date(v.released_at),
				ends_bugfixes_at: new Date(v.ends_bugfixes_at),
				ends_securityfixes_at: new Date(v.ends_securityfixes_at),
			})),
			get yml() {
				const php = this.target_php;
				let yml = ['matrix:'];
				
				yml.push(`  php: [ ${ php.join(', ') } ]`);
				yml.push(`  laravel: [ ${ this.laravel.join(', ') } ]`);
				yml.push(`  dependency-version: [ stable, lowest ]`);
				
				let exclude = [];
				this.targets.forEach((t) => php
					.filter(v => ! t.supported_php.includes(v))
					.reverse()
					.forEach(v => exclude.push(`- { laravel: ${ t.major }.*, php: ${ v } }`)));
				
				if (exclude.length > 0) {
					yml.push(`  exclude:`);
					exclude.forEach(e => yml.push(`    ${ e }`));
				}
				
				return yml.join(`\n`);
			},
			get modes() {
				return ['supported', ...new Set(this.versions.map(v => v.major))];
			},
			get php_modes() {
				return ['all', ...this.php.reverse()];
			},
			get targets() {
				return 'supported' === this.mode
					? this.versions.filter(v => v.ends_bugfixes_at >= now)
					: this.versions.filter(v => v.major >= this.mode);
			},
			get target_php() {
				return 'all' === this.php_mode
					? this.php
					: this.php.filter(v => v >= this.php_mode);
			},
			get php() {
				let php = new Set();
				this.targets.forEach(v => v.supported_php?.forEach(p => php.add(p)));
				return [...php].sort();
			},
			get laravel() {
				return [...new Set(this.targets
					.sort((a, b) => a.major > b.major ? -1 : 1)
					.map(v => `${ v.major }.*`))];
			}
		}));
	});
	</script>
</x-layout>
