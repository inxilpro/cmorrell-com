<?php

namespace App\Http\Controllers;

use App\Support\MarkdownConverter;
use Illuminate\Support\HtmlString;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;

class MarkdownController extends Controller
{
	public function __invoke($_, ...$args)
	{
		abort_unless($page = data_get($args, 'page'), 404);

		$src = file_get_contents(md_path("pages/{$page}.md"));

		$converter = app(MarkdownConverter::class);
		$content = $converter->convert($src);

		$front = $content instanceof RenderedContentWithFrontMatter ? $content->getFrontMatter() : [];

		return view('markdown-page', [
			'og' => data_get($front, 'og', $page),
			'title' => str(data_get($front, 'title'))->append(' - Chris Morrell'),
			'markdown' => new HtmlString($content),
		]);
	}

	public function callAction($method, $parameters)
	{
		// Pass $defaults to __invoke
		return $this($method, ...$parameters);
	}
}
