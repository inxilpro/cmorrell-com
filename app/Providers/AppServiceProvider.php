<?php

namespace App\Providers;

use App\Support\TorchlightManager;
use Illuminate\Support\ServiceProvider;
use Torchlight\Manager;

class AppServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		//
	}
	
	public function boot(): void
	{
		$this->app->booted(function() {
			$this->app->singleton(Manager::class, function() {
				return new TorchlightManager();
			});
		});
	}
}
