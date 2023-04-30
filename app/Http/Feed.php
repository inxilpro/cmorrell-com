<?php

namespace App\Http;

use Illuminate\Support\Facades\Date;
use Spatie\Feed\FeedItem;

class Feed
{
	public static function all()
	{
		return collect([
			FeedItem::create()
				->id('php-fpm')
				->title('Tuning dynamic php-fpm settings')
				->summary('An interactive overview of tuning php-fpm settings')
				->updated(Date::createFromTimestamp(filemtime(resource_path('views/pages/php-fpm.blade.php'))))
				->link(url('/php-fpm'))
				->authorName('Chris Morrell'),
			FeedItem::create()
				->id('laravel-typehint-directive')
				->title('Laravel @typehint Directive')
				->summary('If you use PhpStorm, you might find yourself adding type annotations to your Blade templates from time to time.')
				->updated(Date::createFromTimestamp(filemtime(resource_path('views/pages/laravel-typehint-directive.blade.php'))))
				->link(url('/laravel-typehint-directive'))
				->authorName('Chris Morrell'),
			FeedItem::create()
				->id('a-tale-of-two-methodologies')
				->title('A Tale of Two Methodologies')
				->summary('A case for event sourcing in Laravel')
				->updated(Date::createFromTimestamp(filemtime(resource_path('views/pages/a-tale-of-two-methodologies.blade.php'))))
				->link(url('/a-tale-of-two-methodologies'))
				->authorName('Chris Morrell'),
		]);
	}
}
