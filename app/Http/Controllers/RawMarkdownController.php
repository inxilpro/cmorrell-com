<?php

namespace App\Http\Controllers;

use App\Support\MarkdownConverter;

class RawMarkdownController extends Controller
{
	public function __invoke($_, ...$args)
	{
		abort_unless($page = data_get($args, 'page'), 404);
		abort_unless(preg_match('/[a-z0-9-]+/i', $page), 404);
		
		$src = file_get_contents(md_path("pages/{$page}.md"));
		
		// Just ensure that the file is parsable Markdown
		app(MarkdownConverter::class)->convert($src);
		
		return response($src, headers: ['content-type' => 'text/markdown']);
	}
	
	public function callAction($method, $parameters)
	{
		// Pass $defaults to __invoke
		return $this($method, ...$parameters);
	}
}
