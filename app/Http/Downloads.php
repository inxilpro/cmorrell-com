<?php

namespace App\Http;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Packagist\Api\Client;

class Downloads
{
	public int $total = 0;
	
	public array $data = [];
	
	protected Closure $logger;
	
	public function __construct(
		protected Client $client,
	) {
		$this->logger = fn() => null;
	}
	
	public function __invoke()
	{
		$this->packagist();
		$this->npm();
		
		return $this;
	}
	
	public function setLogger(Closure $logger): static
	{
		$this->logger = $logger;
		
		return $this;
	}
	
	public function npm(): void
	{
		$this->log('Getting all NPM packages...');
		
		$packages = Cache::remember(
			key: "npm:inxilpro:all",
			ttl: now()->addWeek(),
			callback: fn() => Http::get('https://api.npms.io/v2/search?q=maintainer:inxilpro')->json(),
		);
		
		$package_names = collect($packages['results'])->pluck('package.name');
		
		$end = now()->format('Y-m-d');
		foreach ($package_names as $package_name) {
			$this->log("Loading stats for '{$package_name}'...");
			
			$stats = Cache::remember(
				key: "npm:{$package_name}:stats:v2",
				ttl: now()->addDay(),
				callback: fn() => Http::get("https://npm-stat.com/api/download-counts?package={$package_name}&from=2000-01-01&until=$end")->json(),
				// callback: fn() => Http::get("https://api.npmjs.org/downloads/point/2000-01-01:{$end}/{$package_name}")->json(),
			);
			
			$downloads = array_sum(collect($stats)->first(default: []));
			
			$this->total += $downloads;
			$this->data[] = ['npm', $package_name, number_format($downloads)];
		}
	}
	
	public function packagist(): void
	{
		$vendors = [
			'inxilpro',
			'galahad',
			'glhd',
			'internachi',
		];
		
		$package_names = collect();
		
		foreach ($vendors as $vendor) {
			$this->log("Getting all packagist packages for vendor '{$vendor}'...");
			
			$vendor_packages = Cache::remember(
				key: "packagist:{$vendor}:all",
				ttl: now()->addDay(),
				callback: fn() => $this->client->all(['vendor' => $vendor])
			);
			
			$package_names->push(...$vendor_packages);
		}
		
		foreach ($package_names as $package_name) {
			$this->log("Loading stats for '{$package_name}'...");
			
			$package_details = Cache::remember(
				key: "packagist:{$package_name}:stats",
				ttl: now()->addDay(),
				callback: fn() => $this->client->get($package_name)
			);
			
			$downloads = $package_details->getDownloads()->getTotal();
			$this->total += $downloads;
			$this->data[] = ['packagist', $package_name, number_format($downloads)];
		}
	}
	
	protected function log(string $message): void
	{
		call_user_func($this->logger, $message);
	}
}
