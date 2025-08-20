<?php

use App\Support\FinderCollection;
use App\Support\MarkdownConverter;
use Illuminate\Routing\Route as RouteInst;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
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

// Auto-register view/markdown/pages/*.md
FinderCollection::forFiles()
	->in(resource_path('views/markdown/pages'))
	->name('*.md')
	->map(fn(SplFileInfo $info) => $info->getBasename('.md'))
	->reject(fn($basename) => $registered_views->contains("pages.{$basename}"))
	->each(fn($page) => Route::get("/{$page}", function(MarkdownConverter $converter) use ($page) {
		$src = file_get_contents(md_path("pages/{$page}.md"));
		$content = $converter->convert($src);
		$front = $content instanceof RenderedContentWithFrontMatter ? $content->getFrontMatter() : [];
		return view('markdown-page', [
			'og' => data_get($front, 'og', $page),
			'title' => str(data_get($front, 'title'))->append(' - Chris Morrell'),
			'markdown' => new HtmlString($content),
		]);
	})->name("pages.{$page}"));

if (App::isLocal()) {
	Route::view('opengraph', 'opengraph');
}

Route::feeds();
