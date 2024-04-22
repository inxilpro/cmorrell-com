<?php

namespace App\Console\Commands;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Console\Command;
use Throwable;

class GenerateOpenGraphImagesCommand extends Command
{
	protected $signature = 'og:generate';
	
	protected ?RemoteWebDriver $driver = null;
	
	public function handle()
	{
		$this->driver = $this->driver();
		
		register_shutdown_function(function() {
			$this->quit();
		});
		
		try {
			$slugs = [
				'one-billion' => 'Countdown to One Billion',
				'php-fpm' => 'Tuning dynamic php-fpm settings',
			];
			
			foreach ($slugs as $slug => $title) {
				$this->screenShotOpenGraphImage($title, $slug);
			}
		} finally {
			$this->quit();
		}
		
		return 0;
	}
	
	protected function screenShotOpenGraphImage(string $title, string $slug): string
	{
		$url = url('/opengraph?'.http_build_query(['title' => $title, 'url' => "cmorrell.com/{$slug}"]));
		$path = public_path("/opengraph/{$slug}.png");
		
		$this->info($url);
		
		try {
			$this->driver->navigate()->to($url);
			
			$this->driver->wait()
				->until(fn(RemoteWebDriver $driver) => $driver->executeScript('return `complete` === document.readyState'));
			
			$this->driver->findElement(WebDriverBy::id('og-image'))->takeElementScreenshot($path);
			
			$this->info($path);
		} catch (Throwable $exception) {
			$this->error($exception->getMessage());
		}
		
		$this->newLine();
		
		return $path;
	}
	
	public function getSubscribedSignals(): array
	{
		return [
			2, // SIGINT
			15, //SIGTERM
		];
	}
	
	public function handleSignal(int $signal): void
	{
		$this->quit();
		exit(1);
	}
	
	protected function quit()
	{
		if ($this->driver) {
			$this->warn("\nQuitting chromedriver...");
			$this->driver->quit();
			$this->driver = null;
		}
	}
	
	protected function driver()
	{
		$options = new ChromeOptions();
		$options->addArguments([
			'--window-size=1400,1000', // Larger than our og:image size
			'--force-device-scale-factor=2.0', // Higher DPI screenshot
		]);
		
		$capabilities = DesiredCapabilities::chrome();
		$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
		
		return RemoteWebDriver::create('http://localhost:9515', $capabilities);
	}
}
