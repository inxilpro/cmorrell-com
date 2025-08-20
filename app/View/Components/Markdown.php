<?php

namespace App\View\Components;

use App\Support\MarkdownConverter;
use Illuminate\Support\HtmlString;
use Illuminate\View\Component;
use InvalidArgumentException;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;

class Markdown extends Component
{
	public function __construct(
		public ?string $file = null,
	) {
	}
	
	public function render()
	{
		return function($data) {
			$converter = app(MarkdownConverter::class);
			
			$src = match (true) {
				null !== $this->file => file_get_contents($this->file),
				$data['slot']->isNotEmpty() => $this->stripIndent($data['slot']->toHtml()),
				default => throw new InvalidArgumentException('No markdown provided!'),
			};
			
			$html = $converter->convert($src);
			
			if (count($data['attributes']->getAttributes())) {
				$html = "<div {$data['attributes']}>{$html}</div>";
			}
			
			return new HtmlString($html);
		};
	}
	
	public function resolveView()
	{
		return $this->render();
	}
	
	protected function stripIndent(string $markdown): string
	{
		// Because Laravel trims the string, we have to ignore the first line
		$lines = explode("\n", $markdown);
		$first_line = array_shift($lines);
		$other_lines = implode("\n", $lines);
		
		preg_match_all('/^[ \t]*(?=\S)/m', $other_lines, $matches);
		$indent = array_reduce($matches[0], fn($indent, $match) => min($indent, strlen($match)), PHP_INT_MAX);
		
		if (PHP_INT_MAX === $indent) {
			return $markdown;
		}
		
		return $first_line."\n".preg_replace('/^[\t ]{'.$indent.'}/m', '', $other_lines);
	}
}
