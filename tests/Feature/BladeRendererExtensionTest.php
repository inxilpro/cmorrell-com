<?php

namespace Tests\Feature;

use App\Support\BladeRendererExtension;
use App\Support\TitleExtractorExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\MarkdownConverter;
use Tests\TestCase;

class BladeRendererExtensionTest extends TestCase
{
	protected function converter(): MarkdownConverter
	{
		$environment = new Environment();
		$environment->addExtension(new CommonMarkCoreExtension());
		$environment->addExtension(new FrontMatterExtension());
		$environment->addExtension(new TitleExtractorExtension());
		$environment->addExtension(new BladeRendererExtension());

		return new MarkdownConverter($environment);
	}

	public function test_blade_expressions_are_rendered_in_markdown(): void
	{
		$result = $this->converter()->convert("# Hello {{ strtolower('WORLD') }}");

		$this->assertStringContainsString('Hello world', (string) $result);
	}

	public function test_code_blocks_are_protected_from_blade(): void
	{
		$markdown = <<<'MD'
		Some text.

		```php
		{{ $this->shouldNotBeEvaluated }}
		```
		MD;

		$result = (string) $this->converter()->convert($markdown);

		$this->assertStringContainsString('$this-&gt;shouldNotBeEvaluated', $result);
	}

	public function test_inline_code_is_protected_from_blade(): void
	{
		$result = (string) $this->converter()->convert('Use `{{ $variable }}` in your template.');

		$this->assertStringContainsString('<code', $result);
		$this->assertStringContainsString('$variable', $result);
	}

	public function test_front_matter_is_preserved_after_blade_rendering(): void
	{
		$markdown = <<<'MD'
		---
		title: Test Title
		---

		# Hello {{ strtolower('WORLD') }}
		MD;

		$result = $this->converter()->convert($markdown);

		$this->assertInstanceOf(RenderedContentWithFrontMatter::class, $result);
		$this->assertEquals('Test Title', $result->getFrontMatter()['title']);
	}
}
