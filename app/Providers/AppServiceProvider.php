<?php

namespace App\Providers;

use App\Support\TorchlightManager;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Support\DeferringDisplayableValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Torchlight\Manager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->booted(function () {
            $this->app->singleton(Manager::class, function () {
                return new TorchlightManager;
            });
        });

        URL::macro('og', function (string $text, string $url) {
            return $this->signedRoute('og', ['text' => $text, 'url' => $url]);
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

        $this->bootRoute();
    }

    public function bootRoute(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
