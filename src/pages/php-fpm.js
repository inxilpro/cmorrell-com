import React, { useState } from "react";
import Layout from "../components/layout";
import SEO from '../components/seo.js';

const Comment = ({ children }) => <div
	className="text-gray-600 font-mono whitespace-no-wrap"
	children={ children }
/>;

const ConfigLine = ({ children }) => <div
	className="text-gray-900 font-mono whitespace-no-wrap"
	children={ children }
/>;

const Spacer = () => <div className="my-4" />;

export default function PhpFpm() {
	const [average_ram, setAverageRam] = useState(60);
	const [total_ram, setTotalRam] = useState(8);
	const [reserved_ram, setReservedRam] = useState(2);
	
	const available_ram = total_ram - reserved_ram;
	
	const actual_php_fpm_max_children = Math.round((1024 * available_ram) / average_ram);
	const php_fpm_max_children = Math.floor(actual_php_fpm_max_children / 5) * 5;
	const php_fpm_start_servers = Math.floor(php_fpm_max_children / 2 / 5) * 5;
	const php_fpm_max_spare_servers = Math.floor((php_fpm_max_children - (php_fpm_start_servers / 2)) / 5) * 5;
	
	return (
		<Layout>
			
			<SEO title="Tuning dynamic php-fpm settings - Chris Morrell" />
			
			<h1 className="text-5xl lg:text-6xl font-bold font-slant text-gray-800">
				Tuning dynamic php-fpm settings
			</h1>
			
			<p className="text-xl lg:text-2xl leading-normal my-4">
				This is as much a note to self than anything else. Each time I need to
				change my <code>php-fpm</code> settings, I need to Google “php-fpm dynamic tuning”
				or something similar. With a little luck, next time I Google it, I'll find
				this page :)
			</p>
			
			<h2 className="text-xl lg:text-3xl font-bold font-slant my-4">
				Step one: Figuring out how much memory your typical PHP process uses
			</h2>
			
			<p className="text-xl leading-normal my-4">
				First we need to figure out how much memory a typical PHP process uses. This will
				inform the total number of processes that we’re going to run. We can do that with
				this nifty command:
			</p>
			
			<p className="my-4">
				<code children={ `ps --no-headers -o "rss,cmd" -C php-fpm | awk '{ sum+=$1 } END { printf ("%d%s\\n", sum/NR/1024,"M") }'` } />
			</p>
			
			<p className="text-xl leading-normal my-4">
				The <code>ps</code> bit will show all the current running <code>php-fpm</code> processes
				(including their memory consumption), and then the <code>awk</code> bit adds them all up
				and pretty-prints the value in MB.
			</p>
			
			<p className="text-xl leading-normal my-4">
				In the end, this will print out a nice number for us. Something in
				the <strong>40–60 MB range</strong> is to be expected with a typical Laravel app.
			</p>
			
			<h2 className="text-xl lg:text-3xl font-bold font-slant my-4">
				Step two: Deciding how much memory to give to PHP
			</h2>
			
			<p className="text-xl leading-normal my-4">
				This one is entirely up to you. You want to leave some memory for the other
				processes on your server. If the server is dedicated to running PHP only,
				you can dedicate most of your RAM to the php-fpm processes. On the other hand,
				if you’re also running a database server, redis, etc, you’re going to need to
				leave space for those to run.
			</p>
			
			<p className="text-xl leading-normal my-4">
				Keep in mind that if you have queue workers running on your server, they'll
				each take up about the same amount of RAM as your other PHP processes. So,
				for example, if you have 10 queue workers running on your server and your 
				processes take about 50 MB or RAM, that's another 500 MB of RAM that you need
				to set aside.
			</p>
			
			<p className="text-xl leading-normal my-4">
				In my most recent case, I needed to account for the fact that sometimes we
				have other processes running that consume about 1 GB of RAM. To play it safe,
				I decided to reserve 2 GB of RAM for “system and other” processes. On a instance
				with 8 GB of RAM, <strong>that leaves us with 6 GB for PHP</strong>.
			</p>
			
			<h2 className="text-xl lg:text-3xl font-bold font-slant my-4">
				Now, let's let the computers do math for us:
			</h2>
			
			<div className="my-4 border rounded p-4">
				<div className="lg:flex -mx-2">
					<div className="mb-2 flex-1 flex flex-col justify-center mx-2 border rounded">
						<label className="font-bold mb-1 bg-gray-100 text-center p-2">
							Total RAM:
						</label>
						<div className="flex justify-center items-baseline p-2 -mx-1">
							<input
								className="border border-gray-300 p-2 rounded focus:border-gray-500 mx-1"
								type="number"
								min="0"
								step=".5"
								value={ total_ram }
								onChange={ e => setTotalRam(parseFloat(e.target.value)) }
							/>
							<span className="mx-1 font-bold">
							GB
						</span>
						</div>
					</div>
					<div className="mb-2 flex-1 flex flex-col justify-center mx-2 border rounded">
						<label className="font-bold mb-1 bg-gray-100 text-center p-2">
							Reserved RAM:
						</label>
						<div className="flex justify-center items-baseline p-2 -mx-1">
							<input
								className="border border-gray-300 p-2 rounded focus:border-gray-500 mx-1"
								type="number"
								min="0"
								step=".5"
								value={ reserved_ram }
								onChange={ e => setReservedRam(parseFloat(e.target.value)) }
							/>
							<span className="mx-1 font-bold">
							GB
						</span>
						</div>
					</div>
					<div className="mb-2 flex-1 flex flex-col justify-center mx-2 border rounded">
						<label className="font-bold mb-1 bg-gray-100 text-center p-2">
							Average php-fpm process:
						</label>
						<div className="flex justify-center items-baseline p-2 -mx-1">
							<input
								className="border border-gray-300 p-2 rounded focus:border-gray-500 mx-1"
								type="number"
								min="0"
								step=".5"
								value={ average_ram }
								onChange={ e => setAverageRam(parseFloat(e.target.value)) }
							/>
							<span className="mx-1 font-bold">
							MB
						</span>
						</div>
					</div>
				</div>
				<div className="pt-2">
					<h3 className="font-bold mb-1">
						Suggested Settings:
					</h3>
					<div className="bg-gray-100 p-2 rounded mt-4 border text-sm overflow-x-auto w-full">
						<Comment>; Run php-fpm in "dynamic" mode</Comment>
						<ConfigLine>pm = <strong>dynamic</strong></ConfigLine>
						<Spacer />
						<Comment>; Set max_children to ([total RAM - reserved RAM]) / [average php-fpm process])</Comment>
						<Comment>; Most recently: (1024 * ({ total_ram } - { reserved_ram })) / { average_ram } = { actual_php_fpm_max_children }</Comment>
						<ConfigLine>pm.max_children = <strong>{ php_fpm_max_children }</strong></ConfigLine>
						<Spacer />
						<Comment>; When php-fpm starts, have this many processes waiting for requests. Set to 50% of</Comment>
						<Comment>; max on a server that's mostly responsible for running PHP processes</Comment>
						<ConfigLine>pm.start_servers = <strong>{ php_fpm_start_servers }</strong></ConfigLine>
						<Spacer />
						<Comment>; Minimum number spare processes php-fpm will create. In the case of a</Comment>
						<Comment>; server dedicated to running PHP, we'll set this to the same as start_servers</Comment>
						<ConfigLine>pm.min_spare_servers = <strong>{ php_fpm_start_servers }</strong></ConfigLine>
						<Spacer />
						<Comment>; Maximum number spare processes php-fpm will create. If more than this</Comment>
						<Comment>; many processes are idle, some will be killed.</Comment>
						<ConfigLine>pm.max_spare_servers = <strong>{ php_fpm_max_spare_servers }</strong></ConfigLine>
						<Spacer />
						<Comment>; After this many requests, a php-fpm process will respawn. This is useful</Comment>
						<Comment>; to guard against memory leaks, but causes a small performance hit. Set to</Comment>
						<Comment>; a high number (or 0) if you're confident that your app does not have any</Comment>
						<Comment>; memory leaks (and that you're not using any 3rd-party libraries that have</Comment>
						<Comment>; memory leaks), or set to a lower number if you're aware of a leak.</Comment>
						<ConfigLine>pm.max_requests = <strong>500</strong></ConfigLine>
					</div>
				</div>
			</div>
		
		</Layout>
	);
}
