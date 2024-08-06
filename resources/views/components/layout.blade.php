@props([
	'title' => 'Chris Morrell',
	'ogTitle' => null,
	'ogDescription' => '',
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ $title }}</title>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	<script defer data-domain="cmorrell.com" src="https://plausible.io/js/script.js"></script>
	@if($slug)
		<meta property="og:url" content="{{ url()->current() }}" />
		<meta property="og:type" content="website" />
		<meta property="og:title" content="{{ $ogTitle ?? $title }}" />
		<meta property="og:description" content="{{ $ogDescription }}" />
		<meta property="og:image" content="https://cmorrell.com/opengraph/{{ $slug }}.png" />
		<meta name="twitter:card" content="summary_large_image" />
		<meta property="twitter:domain" content="cmorrell.com" />
		<meta property="twitter:url" content="{{ url()->current() }}" />
		<meta name="twitter:creator" content="@inxilpro" />
		<meta name="twitter:title" content="{{ $ogTitle ?? $title }}" />
		<meta name="twitter:description" content="{{ $ogDescription }}" />
		<meta name="twitter:image" content="https://cmorrell.com/opengraph/{{ $slug }}.png" />
	@endif
</head>
@if(app()->isLocal() && 'render' === request('og'))
	<body class="h-full w-full font-sans text-gray-900 antialiased">
	
	<div id="og-image" class="w-[1200px] h-[627px] bg-white mx-auto my-24 flex flex-col">
		<h1 class="mx-auto text-9xl font-slant flex-1 flex items-center justify-center text-center px-10">
			{{ str($ogTitle ?? $title)->beforeLast('- Chris Morrell')->trim() }}
		</h1>
		<div class="bg-black p-4 text-white font-mono mt-auto text-4xl text-right">
			cmorrell.com{{ request()->path() === '/' ? '' : '/'.request()->path() }}
		</div>
	</div>
	
	</body>
@else
	<body class="antialiased">
	<div class="flex flex-col min-h-screen antialiased">
		<header class="bg-gray-900 text-white">
			<div class="container mx-auto p-4 flex items-center">
				<h1 style="margin: 0">
					<a href="{{ url('/') }}" class="text-white hover:underline">
						Chris Morrell
					</a>
				</h1>
				<div class="ml-auto flex -mx-1">
					<a class="mx-1 no-underline opacity-75 hover:opacity-100"
					   href="https://rtsn.dev/@chris"
					   target="_blank"
					   rel="me"
					   title="Chris Morrell on Mastodon"
					>
						<svg class="h-6 w-6 fill-current" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Mastodon</title>
							<path d="M23.268 5.313c-.35-2.578-2.617-4.61-5.304-5.004C17.51.242 15.792 0 11.813 0h-.03c-3.98 0-4.835.242-5.288.309C3.882.692 1.496 2.518.917 5.127.64 6.412.61 7.837.661 9.143c.074 1.874.088 3.745.26 5.611.118 1.24.325 2.47.62 3.68.55 2.237 2.777 4.098 4.96 4.857 2.336.792 4.849.923 7.256.38.265-.061.527-.132.786-.213.585-.184 1.27-.39 1.774-.753a.057.057 0 0 0 .023-.043v-1.809a.052.052 0 0 0-.02-.041.053.053 0 0 0-.046-.01 20.282 20.282 0 0 1-4.709.545c-2.73 0-3.463-1.284-3.674-1.818a5.593 5.593 0 0 1-.319-1.433.053.053 0 0 1 .066-.054c1.517.363 3.072.546 4.632.546.376 0 .75 0 1.125-.01 1.57-.044 3.224-.124 4.768-.422.038-.008.077-.015.11-.024 2.435-.464 4.753-1.92 4.989-5.604.008-.145.03-1.52.03-1.67.002-.512.167-3.63-.024-5.545zm-3.748 9.195h-2.561V8.29c0-1.309-.55-1.976-1.67-1.976-1.23 0-1.846.79-1.846 2.35v3.403h-2.546V8.663c0-1.56-.617-2.35-1.848-2.35-1.112 0-1.668.668-1.67 1.977v6.218H4.822V8.102c0-1.31.337-2.35 1.011-3.12.696-.77 1.608-1.164 2.74-1.164 1.311 0 2.302.5 2.962 1.498l.638 1.06.638-1.06c.66-.999 1.65-1.498 2.96-1.498 1.13 0 2.043.395 2.74 1.164.675.77 1.012 1.81 1.012 3.12z" />
						</svg>
					</a>
					<a class="mx-1 no-underline opacity-75 hover:opacity-100"
					   href="https://twitter.com/inxilpro"
					   target="_blank"
					   rel="noopener noreferrer"
					   title="Chris Morrell on Twitter"
					>
						<svg class="h-6 w-6 fill-current" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Twitter</title>
							<path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
						</svg>
					</a>
					<a class="mx-1 no-underline opacity-75 hover:opacity-100"
					   href="https://github.com/inxilpro/"
					   target="_blank"
					   rel="noopener noreferrer"
					   title="Chris Morrell on Github"
					>
						<svg class="h-6 w-6 fill-current" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>GitHub</title>
							<path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12" />
						</svg>
					</a>
				</div>
			</div>
		</header>
		<main class="flex-1">
			@if(isset($main) && $main->isNotEmpty())
				{{ $main }}
			@else
				<div class="max-w-4xl mx-auto p-4">
					{{ $slot }}
				</div>
			@endif
		</main>
		<footer class="bg-gray-100">
			<div class="container mx-auto p-4 py-8 pb-12 text-sm text-gray-700">
				&copy; 1997–{{ now()->year }} Chris Morrell
			</div>
		</footer>
	</div>
	</body>
@endif
</html>
