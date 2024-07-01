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
		
		foreach ($package_names as $package_name) {
			$this->log("Loading stats for '{$package_name}'...");
			
			$downloads = Cache::remember(
				key: "npm:{$package_name}:download_sum:v1",
				ttl: now()->addDay(),
				callback: fn() => $this->npmPackage($package_name),
			);
			
			$this->total += $downloads;
			$this->data[] = ['npm', $package_name, number_format($downloads)];
		}
	}
	
	protected function npmPackage(string $package_name): int
	{
		$count = 0;
		$start = now()->subYearNoOverflow();
		$end = now();
		
		do {
			$url = sprintf(
				'https://npm-trends-proxy.uidotdev.workers.dev/npm/downloads/range/%s:%s/%s',
				$start->format('Y-m-d'), $end->format('Y-m-d'), $package_name
			);
			
			$response = Http::get($url);
			
			if (! empty($response->json('error'))) {
				break;
			}
			
			$count += collect($response->json('downloads'))->pluck('downloads')->sum();
			
			$end = $start->toImmutable()->subDay();
			$start = $end->toImmutable()->subYearNoOverflow();
		} while ($response->ok());
		
		return $count;
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
