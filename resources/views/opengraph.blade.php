<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	@vite('resources/css/app.css')
</head>
<body class="h-full w-full font-sans text-gray-900 antialiased">

<div id="og-image" class="w-[1200px] h-[627px] bg-white mx-auto my-24 flex flex-col">
	<h1 class="mx-auto text-9xl font-slant flex-1 flex items-center justify-center text-center px-10">
		{{ request('title') ?? 'Chris Morrell' }}
	</h1>
	<div class="bg-black p-4 text-white mt-auto text-4xl">
		{{ request('url') ?? 'cmorrell.com' }}
	</div>
</div>

</body>
</html>
