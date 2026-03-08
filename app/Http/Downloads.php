<?php

namespace App\Http;

use Closure;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Packagist\Api\Client;
use Throwable;

class Downloads
{
	public int $total = 0;
	
	public array $data = [];
	
	public bool $refresh_zeroes = false;
	
	public bool $refresh_all = false;
	
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
		
		Cache::forever('downloads:total', $this->total);
		
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
		
		$packages = Cache::flexible(
			key: 'npm:inxilpro:all',
			ttl: [now()->addWeek(), now()->addMonth()],
			callback: fn() => Http::get('https://api.npms.io/v2/search?q=maintainer:inxilpro')->json(),
		);
		
		$package_names = collect($packages['results'])->pluck('package.name');
		
		foreach ($package_names as $package_name) {
			$this->log("Loading stats for '{$package_name}'...");
			
			$downloads = $this->npmPackageWithCache($package_name);
			
			if (0 === $downloads && $this->refresh_zeroes) {
				$downloads = $this->npmPackageWithCache($package_name, refresh_cache: true);
			}
			
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
		
		$packages = ['hirethunk/verbs'];
		
		$package_names = collect($packages);
		
		foreach ($vendors as $vendor) {
			$this->log("Getting all packagist packages for vendor '{$vendor}'...");
			
			$vendor_packages = Cache::flexible(
				key: "packagist:{$vendor}:all",
				ttl: [now()->addDay(), now()->addMonth()],
				callback: fn() => $this->client->all(['vendor' => $vendor])
			);
			
			$package_names->push(...$vendor_packages);
		}
		
		foreach ($package_names as $package_name) {
			$this->log("Loading stats for '{$package_name}'...");
			
			$package_details = Cache::flexible(
				key: "packagist:{$package_name}:stats",
				ttl: [now()->addDay(), now()->addMonth()],
				callback: fn() => $this->client->get($package_name),
			);
			
			$downloads = $package_details->getDownloads()->getTotal();
			$this->total += $downloads;
			$this->data[] = ['packagist', $package_name, number_format($downloads)];
		}
	}
	
	protected function npmPackageWithCache(string $package_name, bool $refresh_cache = false): int
	{
		$key = "npm:{$package_name}:download_sum:v1";
		
		if ($refresh_cache) {
			Cache::forget($key);
		}
		
		return Cache::flexible(
			key: $key,
			ttl: [now()->addDay(), now()->addMonth()],
			callback: fn() => $this->npmPackage($package_name),
		);
	}
	
	protected function npmPackage(string $package_name): int
	{
		$count = 0;
		$end = now();
		
		// The NPM API silently caps at ~18 months per request, so we chunk
		// into 500-day segments to capture the full download history
		do {
			$start = $end->toImmutable()->subDays(500);
			
			if (! $end->isToday()) {
				$this->log(" - Fetching for range {$start->format('Y-m-d')} to {$end->format('Y-m-d')} (at {$count})...");
			}
			
			$url = sprintf(
				'https://api.npmjs.org/downloads/point/%s:%s/%s',
				$start->format('Y-m-d'),
				$end->format('Y-m-d'),
				$package_name
			);
			
			$response = Http::createPendingRequest()
				->retry(
					times: 4,
					sleepMilliseconds: function(int $attempt, Exception $exception) {
						$code = $exception->response?->status() ?? 500;
						$delay = $attempt * 5000;
						
						if (429 === $code) {
							if ($retry_after = $exception->response?->header('Retry-After')) {
								$delay = 1 + ((int) $retry_after * 1000);
							}
							$this->log(" - Rate limited, waiting {$delay}ms before retry {$attempt}...");
						} else {
							$this->log(" - Got a {$code} error, waiting {$delay}ms before retry {$attempt}...");
						}
						
						return $delay;
					},
					when: function(Throwable $exception, PendingRequest $request) {
						// Only retry on non 400 (bad request) errors
						return ! ($exception instanceof RequestException)
							|| 400 !== $exception->response->status();
					},
					throw: false
				)
				->get($url);
			
			if ($response->failed() || $response->json('error')) {
				$body = $response->body();
				if (str_contains($body, 'end date > start date')) {
					$this->log(' - Reached beginning of package date range');
				} else {
					$this->log(" - Failed due to a {$response->status()} error. Response: '{$body}'");
				}
				break;
			}
			
			$count += $response->json('downloads', 0);
			
			$end = $start->subDay();
		} while (true);
		
		return $count;
	}
	
	protected function log(string $message): void
	{
		call_user_func($this->logger, $message);
	}
}
