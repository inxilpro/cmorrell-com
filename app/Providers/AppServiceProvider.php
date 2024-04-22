<?php

namespace App\Providers;

use App\Support\TorchlightManager;
use Illuminate\Contracts\Support\DeferringDisplayableValue;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
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
		
		View::share('slug', new class implements DeferringDisplayableValue
		{
			public function resolveDisplayableValue()
			{
				$path = trim(request()->path(), '/');
				
				return match ($path) {
					'' => 'home',
					default => Str::slug(strtolower($path)),
				};
			}
		});
	}
}
