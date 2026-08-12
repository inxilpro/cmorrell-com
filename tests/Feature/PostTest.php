<?php

namespace Tests\Feature;

use App\Support\Post;
use Tests\TestCase;

class PostTest extends TestCase
{
	public function test_all_returns_collection_of_posts(): void
	{
		$posts = Post::all();

		$this->assertGreaterThan(0, $posts->count());
		$this->assertContainsOnlyInstancesOf(Post::class, $posts);
	}

	public function test_posts_have_metadata_from_front_matter(): void
	{
		$post = Post::find('llms-in-mid-2025');

		$this->assertNotNull($post);
		$this->assertEquals('LLMs in mid-2025', $post->title);
		$this->assertNotNull($post->published_at);
		$this->assertEquals('2025-06-15', $post->published_at->format('Y-m-d'));
		$this->assertEquals('Thoughts on the current state of LLMs', $post->summary);
	}

	public function test_published_returns_only_posts_with_dates(): void
	{
		$published = Post::published();

		$this->assertGreaterThan(0, $published->count());

		$published->each(function(Post $post) {
			$this->assertNotNull($post->published_at, "Post '{$post->slug}' should have a date");
		});
	}

	public function test_published_is_sorted_newest_first(): void
	{
		$published = Post::published();

		$dates = $published->map(fn(Post $p) => $p->published_at->timestamp)->all();

		for ($i = 1; $i < count($dates); $i++) {
			$this->assertGreaterThanOrEqual($dates[$i], $dates[$i - 1]);
		}
	}

	public function test_find_returns_null_for_nonexistent_slug(): void
	{
		$this->assertNull(Post::find('this-post-does-not-exist'));
	}

	public function test_post_title_falls_back_to_slug(): void
	{
		$post = Post::fromFile(resource_path('views/markdown/pages/llms-in-mid-2025.md'));

		$this->assertEquals('llms-in-mid-2025', $post->slug);
	}

	public function test_rss_feed_includes_published_posts(): void
	{
		$response = $this->get('/rss');

		$response->assertOk();

		$published = Post::published();

		foreach ($published as $post) {
			$response->assertSee($post->title);
		}
	}

	public function test_home_page_lists_published_posts(): void
	{
		$response = $this->get('/');

		$response->assertOk();

		$published = Post::published();

		foreach ($published as $post) {
			$response->assertSee($post->title);
		}
	}
}
