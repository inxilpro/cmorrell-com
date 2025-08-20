<?php

use App\Support\FinderCollection;
use Illuminate\Routing\Route as RouteInst;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Finder\SplFileInfo;

Route::view('/', 'pages.home');

$registered_views = collect(Route::getRoutes()->getRoutes())
	->filter(fn(RouteInst $route) => data_get($route->action, 'controller') === '\Illuminate\Routing\ViewController')
	->map(fn(RouteInst $route) => $route->defaults['view']);

// Auto-register view/pages/*.blade.php
FinderCollection::forFiles()
	->in(resource_path('views/pages'))
	->name('*.blade.php')
	->map(fn(SplFileInfo $info) => $info->getBasename('.blade.php'))
	->reject(fn($basename) => $registered_views->contains("pages.{$basename}"))
	->each(fn($page) => Route::view("/{$page}", "pages.{$page}")->name("pages.{$page}"));

if (App::isLocal()) {
	Route::view('opengraph', 'opengraph');
}

Route::feeds();
