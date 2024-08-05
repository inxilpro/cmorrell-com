<?php

namespace App\View\Components;

use Illuminate\Support\HtmlString;
use Illuminate\View\Component;
use InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Node\Block\Paragraph;
use Torchlight\Commonmark\V2\TorchlightExtension;

class Markdown extends Component
{
	public function __construct(
		public ?string $file = null,
	) {
	}
	
	public function render()
	{
		return function($data) {
			$converter = new GithubFlavoredMarkdownConverter($this->config());
			
			$converter->getEnvironment()
				->addExtension(new DefaultAttributesExtension())
				->addExtension(new TorchlightExtension());
			
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
	
	protected function config(): array
	{
		return [
			'default_attributes' => [
				Heading::class => [
					'class' => static fn(Heading $node) => match ($node->getLevel()) {
						1 => 'text-5xl text-center font-bold font-slant text-gray-800 sm:text-left lg:text-6xl',
						2 => 'text-2xl text-center font-bold font-slant my-4 sm:text-left lg:text-3xl',
						3 => 'text-xl lg:text-3xl font-bold font-slant mt-6 mb-2 text-gray-600',
						default => 'text-xl lg:text-2xl font-bold mt-6 mb-1',
					},
				],
				Paragraph::class => [
					'class' => 'text-lg lg:text-xl leading-normal mb-4',
				],
				BlockQuote::class => [
					'class' => 'ml-6 pl-4 border-l-4 border-gray-100 italic text-gray-600 text-lg lg:text-xl leading-normal mb-4',
				],
				IndentedCode::class => [
					'class' => 'block w-full overflow-x-auto leading-normal mb-4',
				],
				FencedCode::class => [
					'class' => 'block w-full leading-normal mb-4 overflow-x-auto',
				],
				Code::class => [
					'class' => 'inline-block bg-gray-50 border border-gray-100 rounded font-mono px-2 py-0 m-0 text-purple-600',
				],
				ListBlock::class => [
					'class' => static fn(ListBlock $node) => match ($node->getListData()->bulletChar) {
						'*', '-' => 'pl-12 my-4 list-disc',
						default => 'pl-12 my-4 list-decimal',
					},
				],
				ListItem::class => [
					'class' => 'text-lg lg:text-xl leading-normal mb-4',
				],
				Link::class => [
					'class' => 'text-blue-800 underline hover:text-blue-500',
				],
				Image::class => [
					'class' => 'rounded-lg my-8',
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
