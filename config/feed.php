<?php

use App\Http\Feed;

return [
	'feeds' => [
		'main' => [
			'items' => [Feed::class, 'all'],
			'url' => '/rss',
			'title' => 'Chris Morrell',
			'description' => 'Posts and articles by Chris Morrell',
			'language' => 'en-US',
			// 'image' => '',
			'format' => 'rss',
		],
	],
];
