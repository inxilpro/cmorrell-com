<?php

namespace App\View\Components;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Node\Block\Paragraph;
use Torchlight\Commonmark\V2\TorchlightExtension;

class Markdown extends Component
{
	public function render()
	{
		return function($data) {
			$converter = new GithubFlavoredMarkdownConverter($this->config());
			
			$converter->getEnvironment()
				->addExtension(new DefaultAttributesExtension())
				->addExtension(new TorchlightExtension());
			
			return new HtmlString($converter->convert($this->stripIndent($data['slot']->toHtml())));
		};
	}
	
	protected function config(): array
	{
		return [
			'default_attributes' => [
				Heading::class => [
					'class' => static fn(Heading $node) => match($node->getLevel()) {
						1 => 'text-5xl lg:text-6xl font-bold font-slant text-gray-800',
						2 => 'text-xl lg:text-3xl font-bold font-slant my-4',
						3 => 'text-xl lg:text-3xl font-bold font-slant mt-6 mb-2 text-gray-600',
						default => 'text-xl lg:text-2xl font-bold mt-6 mb-1',
					}
				],
				Paragraph::class => [
					'class' => 'text-xl lg:text-2xl leading-normal mb-4',
				],
				IndentedCode::class => [
					'class' => 'block w-full leading-normal mb-4',
				],
				FencedCode::class => [
					'class' => 'block w-full leading-normal mb-4',
				],
				ListBlock::class => [
					'class' => static fn(ListBlock $node) => match($node->getListData()->bulletChar) {
						'*' => 'pl-12 my-4 list-disc',
						default => 'pl-12 my-4 list-decimal',
					},
				],
				ListItem::class => [
					'class' => 'text-xl lg:text-2xl leading-normal mb-4',
				],
			],
		];
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
