<?php

namespace App\Http;

use App\Support\Post;
use Spatie\Feed\FeedItem;

class Feed
{
	public static function all()
	{
		return Post::published()
			->map(
				fn(Post $post) => FeedItem::create()
					->id($post->slug)
					->title($post->title)
					->summary($post->summary ?? '')
					->updated($post->published_at)
					->link(url("/{$post->slug}"))
					->authorName('Chris Morrell')
			);
	}
}
