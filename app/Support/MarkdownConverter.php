<?php

namespace App\Support;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
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
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Node\Block\Paragraph;
use Torchlight\Commonmark\V2\TorchlightExtension;

class MarkdownConverter extends \League\CommonMark\MarkdownConverter
{
	public function __construct()
	{
		$environment = new Environment($this->config());

		// Github-flavored extensions
		$environment->addExtension(new CommonMarkCoreExtension());
		$environment->addExtension(new GithubFlavoredMarkdownExtension());

		// Custom extensions
		$environment->addExtension(new DefaultAttributesExtension());
		$environment->addExtension(new TorchlightExtension());
		$environment->addExtension(new FrontMatterExtension());
		$environment->addExtension(new TitleExtractorExtension());

		parent::__construct($environment);
	}

	public function getEnvironment(): Environment
	{
		return $this->environment;
	}

	protected function config(): array
	{
		return [
			'default_attributes' => [
				Heading::class => [
					'class' => static fn(Heading $node) => match ($node->getLevel()) {
						1 => 'text-5xl text-center font-bold font-slant text-gray-800 mb-8 sm:text-left lg:text-6xl',
						2 => 'text-2xl text-center font-bold font-slant my-4 mt-8 sm:text-left lg:text-3xl',
						3 => 'text-xl lg:text-3xl font-bold font-slant mt-8 mb-2 text-gray-600',
						default => 'text-xl lg:text-2xl font-bold mt-6 mb-1',
					},
				],
				Paragraph::class => [
					'class' => 'text-lg lg:text-xl leading-normal mb-4',
				],
				BlockQuote::class => [
					'class' => 'ml-6 pl-4 border-l-4 border-gray-100 font-serif italic text-gray-600 text-lg lg:text-xl leading-normal mb-4',
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
}
