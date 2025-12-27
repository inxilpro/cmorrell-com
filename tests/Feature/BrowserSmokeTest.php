<?php

use Illuminate\Support\Facades\Cache;

test('pages render without errors', function() {
	Cache::put('packagist:inxilpro:all', ['inxilpro/smoke-test']);
	Cache::put('packagist:galahad:all', []);
	Cache::put('packagist:glhd:all', []);
	Cache::put('packagist:internachi:all', []);
	Cache::put('packagist:inxilpro/smoke-test:stats', fakePackageStats());
	Cache::put('packagist:hirethunk/verbs:stats', fakePackageStats());
	Cache::put('npm:inxilpro:all', ['results' => [['package' => ['name' => 'smoke-test']]]]);
	Cache::put('npm:smoke-test:download_sum:v1', 1);
	
	$routes = collect(Route::getRoutes()->getRoutes())
		->map(fn(\Illuminate\Routing\Route $r) => $r->getName())
		->reject(fn($name) => ! str_starts_with($name, 'pages.'))
		->map(fn($name) => (string) str(route($name))->after(url()->to('/')))
		->push('/')
		->unique()
		->all();
	
	visit($routes)
		// ->assertDontSee('Server Error')
		->assertNoSmoke();
});
