<?php

namespace App\Console\Commands;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Throwable;

class GenerateOpenGraphImagesCommand extends Command
{
    protected $signature = 'og:generate';

    protected ?RemoteWebDriver $driver = null;

    public function handle(): int
    {
        $this->driver = $this->driver();

        register_shutdown_function(function () {
            $this->quit();
        });

        $routes = app(Router::class)->getRoutes();

        try {
            foreach ($routes as $route) {
                $this->screenShotOpenGraphImage($route);
            }
        } finally {
            $this->quit();
        }

        return 0;
    }

    protected function screenShotOpenGraphImage(Route $route): string
    {
        $url = url($route->uri().'?og=render');
        $path = ltrim(parse_url($route->uri(), PHP_URL_PATH), '/');
        $slug = match ($path) {
            '' => 'home',
            default => Str::slug(strtolower($path)),
        };

        $filename = public_path("/opengraph/{$slug}.png");

        $this->info($url);

        try {
            $this->driver->navigate()->to($url);

            $this->driver->wait()
                ->until(fn (RemoteWebDriver $driver) => $driver->executeScript('return `complete` === document.readyState'));

            $this->driver->findElement(WebDriverBy::id('og-image'))->takeElementScreenshot($filename);

            $this->info($filename);
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
            15, // SIGTERM
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
        $options = new ChromeOptions;
        $options->addArguments([
            '--window-size=1400,1000', // Larger than our og:image size
            '--force-device-scale-factor=2.0', // Higher DPI screenshot
        ]);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        return RemoteWebDriver::create('http://localhost:9515', $capabilities);
    }
}
