<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Symfony\Component\Yaml\Yaml;

class Post
{
	public function __construct(
		public string $slug,
		public string $title,
		public ?CarbonInterface $published_at = null,
		public ?string $summary = null,
		public string|array|null $og = null,
		public ?string $listing = null,
		public string $file_path = '',
	) {
	}
	
	public static function fromFile(string $path): static
	{
		$slug = pathinfo($path, PATHINFO_FILENAME);
		$contents = file_get_contents($path);
		
		$frontmatter = [];
		if (preg_match('/\A---\s*\n(.*?)\n---\s*\n/s', $contents, $matches)) {
			$frontmatter = Yaml::parse($matches[1]) ?? [];
		}
		
		$published_at = data_get($frontmatter, 'published_at');
		
		return new static(
			slug: $slug,
			title: data_get($frontmatter, 'title', str($slug)->headline()->toString()),
			published_at: $published_at ? Date::createFromTimestamp($published_at, config('app.timezone')) : null,
			summary: data_get($frontmatter, 'summary'),
			og: data_get($frontmatter, 'og'),
			listing: data_get($frontmatter, 'listing'),
			file_path: $path,
		);
	}
	
	/** @return Enumerable<int, static> */
	public static function all(): Enumerable
	{
		$callback = fn() => FinderCollection::forFiles()
			->in(resource_path('views/markdown/pages'))
			->name('*.md')
			->map(static fn($file) => static::fromFile($file->getRealPath()))
			->collect();
		
		if (app()->isProduction()) {
			return Cache::rememberForever('posts:all', $callback);
		}
		
		return $callback();
	}
	
	/** @return Enumerable<int, static> */
	public static function published(): Enumerable
	{
		return static::all()
			->filter(static fn(self $post) => $post->published_at !== null)
			->sortByDesc(static fn(self $post) => $post->published_at)
			->values();
	}
	
	public static function find(string $slug): ?static
	{
		return static::all()->first(fn(self $post) => $post->slug === $slug);
	}
}
