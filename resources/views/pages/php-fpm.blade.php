<x-layout title="Tuning dynamic php-fpm settings - Chris Morrell" og="php-fpm">
	
	<h1 class="text-5xl lg:text-6xl font-bold font-slant text-gray-800">
		Tuning dynamic php-fpm settings
	</h1>
	
	<p class="text-xl lg:text-2xl leading-normal my-4">
		This is as much a note to self than anything else. Each time I need to
		change my <code>php-fpm</code> settings, I need to Google “php-fpm dynamic tuning”
		or something similar. With a little luck, next time I Google it, I'll find
		this page :)
	</p>
	
	<h2 class="text-xl lg:text-3xl font-bold font-slant my-4">
		Step one: Figuring out how much memory your typical PHP process uses
	</h2>
	
	<p class="text-xl leading-normal my-4">
		First we need to figure out how much memory a typical PHP process uses. This will
		inform the total number of processes that we’re going to run. We can do that with
		this nifty command:
	</p>
	
	<p class="my-4">
		<code>ps --no-headers -o "rss,cmd" -C php-fpm | awk '{ sum+=$1 } END { printf ("%d%s\\n", sum/NR/1024,"M") }'</code>
	</p>
	
	<p class="text-xl leading-normal my-4">
		The <code>ps</code> bit will show all the current running <code>php-fpm</code> processes
		(including their memory consumption), and then the <code>awk</code> bit adds them all up
		and pretty-prints the value in MB.
	</p>
	
	<p class="text-xl leading-normal my-4">
		In the end, this will print out a nice number for us. Something in
		the <strong>40–60 MB range</strong> is to be expected with a typical Laravel app.
	</p>
	
	<h2 class="text-xl lg:text-3xl font-bold font-slant my-4">
		Step two: Deciding how much memory to give to PHP
	</h2>
	
	<p class="text-xl leading-normal my-4">
		This one is entirely up to you. You want to leave some memory for the other
		processes on your server. If the server is dedicated to running PHP only,
		you can dedicate most of your RAM to the php-fpm processes. On the other hand,
		if you’re also running a database server, redis, etc, you’re going to need to
		leave space for those to run.
	</p>
	
	<p class="text-xl leading-normal my-4">
		Keep in mind that if you have queue workers running on your server, they'll
		each take up about the same amount of RAM as your other PHP processes. So,
		for example, if you have 10 queue workers running on your server and your
		processes take about 50 MB or RAM, that's another 500 MB of RAM that you need
		to set aside.
	</p>
	
	<p class="text-xl leading-normal my-4">
		In my most recent case, I needed to account for the fact that sometimes we
		have other processes running that consume about 1 GB of RAM. To play it safe,
		I decided to reserve 2 GB of RAM for “system and other” processes. On a instance
		with 8 GB of RAM, <strong>that leaves us with 6 GB for PHP</strong>.
	</p>
	
	<h2 class="text-xl lg:text-3xl font-bold font-slant my-4">
		Now, let's let the computers do math for us:
	</h2>
	
	<div x-data="tuning" class="my-4 border rounded p-4">
		<div class="lg:flex -mx-2">
			<div class="mb-2 flex-1 flex flex-col justify-center mx-2 border rounded">
				<label for="total_ram" class="font-bold mb-1 bg-gray-100 text-center p-2">
					Total RAM:
				</label>
				<div class="flex justify-center items-baseline p-2 -mx-1">
					<input
						id="total_ram"
						class="border border-gray-300 p-2 rounded focus:border-gray-500 mx-1"
						type="number"
						min="0"
						step=".5"
						x-model.number="total_ram"
					/>
					<span class="mx-1 font-bold">
						GB
					</span>
				</div>
			</div>
			<div class="mb-2 flex-1 flex flex-col justify-center mx-2 border rounded">
				<label for="reserved_ram" class="font-bold mb-1 bg-gray-100 text-center p-2">
					Reserved RAM:
				</label>
				<div class="flex justify-center items-baseline p-2 -mx-1">
					<input
						id="reserved_ram"
						class="border border-gray-300 p-2 rounded focus:border-gray-500 mx-1"
						type="number"
						min="0"
						step=".5"
						x-model.number="reserved_ram"
					/>
					<span class="mx-1 font-bold">
						GB
					</span>
				</div>
			</div>
			<div class="mb-2 flex-1 flex flex-col justify-center mx-2 border rounded">
				<label for="average_ram" class="font-bold mb-1 bg-gray-100 text-center p-2">
					Average php-fpm process:
				</label>
				<div class="flex justify-center items-baseline p-2 -mx-1">
					<input
						id="average_ram"
						class="border border-gray-300 p-2 rounded focus:border-gray-500 mx-1"
						type="number"
						min="0"
						step=".5"
						x-model.number="average_ram"
					/>
					<span class="mx-1 font-bold">
						MB
					</span>
				</div>
			</div>
		</div>
		<div class="pt-2">
			<h3 class="font-bold mb-1">
				Suggested Settings:
			</h3>
			
			<div class="bg-gray-100 p-2 rounded mt-4 border text-sm overflow-x-auto w-full">
				<div class="text-gray-600 font-mono whitespace-no-wrap">; Run php-fpm in "dynamic" mode</div>
				<div class="text-gray-900 font-mono whitespace-no-wrap">pm = <strong>dynamic</strong></div>
				<div class="my-4"></div>
				<div class="text-gray-600 font-mono whitespace-no-wrap">; Set max_children to ([total RAM - reserved RAM]) / [average php-fpm process])</div>
				<div class="text-gray-600 font-mono whitespace-no-wrap">; Most recently: (1024 * (<span x-text="total_ram">8</span> - <span x-text="reserved_ram">2</span>)) /
					<span x-text="average_ram">60</span> = <span x-text="actual_php_fpm_max_children">102</span></div>
				<div class="text-gray-900 font-mono whitespace-no-wrap">pm.max_children = <strong x-text="php_fpm_max_children">100</strong></div>
				<div class="my-4"></div>
				<div class="text-gray-600 font-mono whitespace-no-wrap">; When php-fpm starts, have this many processes waiting for requests. Set to 50% of</div>
				<div class="text-gray-600 font-mono whitespace-no-wrap">; max on a server that's mostly responsible for running PHP processes</div>
				<div class="text-gray-900 font-mono whitespace-no-wrap">pm.start_servers = <strong x-text="php_fpm_start_servers">50</strong></div>
				<div class="my-4"></div>
				<div class="text-gray-600 font-mono whitespace-no-wrap">; Minimum number spare processes php-fpm will create. In the case of a server</div>
				<div class="text-gray-600 font-mono whitespace-no-wrap">; dedicated to running PHP, we'll set this to about 1/3 of the remaining capacity</div>
				<div class="text-gray-900 font-mono whitespace-no-wrap">pm.min_spare_servers = <strong x-text="php_fpm_min_spare_servers">15</strong></div>
				<div class="my-4"></div>
				<div class="text-gray-600 font-mono whitespace-no-wrap">; Maximum number spare processes php-fpm will create. If more than this</div>
				<div class="text-gray-600 font-mono whitespace-no-wrap">; many processes are idle, some will be killed.</div>
				<div class="text-gray-900 font-mono whitespace-no-wrap">pm.max_spare_servers = <strong x-text="php_fpm_max_spare_servers">40</strong></div>
				<div class="my-4"></div>
				<div class="text-gray-600 font-mono whitespace-no-wrap">; After this many requests, a php-fpm process will respawn. This is useful</div>
				<div class="text-gray-600 font-mono whitespace-no-wrap">; to guard against memory leaks, but causes a small performance hit. Set to</div>
				<div class="text-gray-600 font-mono whitespace-no-wrap">; a high number (or 0) if you're confident that your app does not have any</div>
				<div class="text-gray-600 font-mono whitespace-no-wrap">; memory leaks (and that you're not using any 3rd-party libraries that have</div>
				<div class="text-gray-600 font-mono whitespace-no-wrap">; memory leaks), or set to a lower number if you're aware of a leak.</div>
				<div class="text-gray-900 font-mono whitespace-no-wrap">pm.max_requests = <strong>500</strong></div>
			</div>
		</div>
	</div>
	
	<script>
	document.addEventListener('alpine:init', () => {
		Alpine.data('tuning', () => ({
			total_ram: 8,
			average_ram: 60,
			reserved_ram: 2,
			get available_ram() {
				return this.total_ram - this.reserved_ram;
			},
			get actual_php_fpm_max_children() {
				return Math.round((1024 * this.available_ram) / this.average_ram);
			},
			get php_fpm_max_children() {
				return Math.floor(this.actual_php_fpm_max_children / 5) * 5;
			},
			get php_fpm_start_servers() {
				return Math.floor(this.php_fpm_max_children / 2 / 5) * 5;
			},
			get php_fpm_min_spare_servers() {
				return Math.floor((this.php_fpm_max_children - this.php_fpm_start_servers) / 3 / 5) * 5;
			},
			get php_fpm_max_spare_servers() {
				return Math.floor((this.php_fpm_max_children - this.php_fpm_start_servers) / 1.25 / 5) * 5;
			},
		}));
	});
	</script>

</x-layout>
