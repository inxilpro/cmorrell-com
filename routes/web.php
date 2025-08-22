<?php

use App\Http\Controllers\MarkdownController;
use App\Support\FinderCollection;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Finder\SplFileInfo;

// Auto-register view/markdown/pages/*.md (lowest priority)
FinderCollection::forFiles()
    ->in(resource_path('views/markdown/pages'))
    ->name('*.md')
    ->map(fn (SplFileInfo $info) => $info->getBasename('.md'))
    ->each(fn ($page) => Route::get("/{$page}", MarkdownController::class)
        ->defaults('page', $page)
        ->name("pages.{$page}"));

// Auto-register view/pages/*.blade.php (higher priority)
FinderCollection::forFiles()
    ->in(resource_path('views/pages'))
    ->name('*.blade.php')
    ->map(fn (SplFileInfo $info) => $info->getBasename('.blade.php'))
    ->each(fn ($page) => Route::view("/{$page}", "pages.{$page}")
        ->name("pages.{$page}"));

// Manually register views (highest priority)
Route::view('/', 'pages.home');

// Local-only routes
if (App::isLocal()) {
    Route::view('opengraph', 'opengraph');
}

Route::feeds();
