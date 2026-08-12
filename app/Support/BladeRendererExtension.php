<?php

namespace App\Support;

use Illuminate\Support\Facades\Blade;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Event\DocumentRenderedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Node\Node;
use League\CommonMark\Output\RenderedContent;
use League\CommonMark\Renderer\HtmlRenderer;

class BladeRendererExtension implements ExtensionInterface
{
	protected array $code_block_map = [];
	
	protected Environment $environment;
	
	public function register(EnvironmentBuilderInterface $environment): void
	{
		$this->environment = $environment;
		
		$environment->addEventListener(DocumentParsedEvent::class, $this->onDocumentParsed(...), -10);
		$environment->addEventListener(DocumentRenderedEvent::class, $this->onDocumentRendered(...), 10);
	}
	
	protected function onDocumentParsed(DocumentParsedEvent $event): void
	{
		$this->code_block_map = [];
		$this->replaceCodeNodes($event->getDocument());
	}
	
	protected function onDocumentRendered(DocumentRenderedEvent $event): void
	{
		$output = $event->getOutput();
		
		$content = str_replace(
			search: array_keys($this->code_block_map),
			replace: array_values($this->code_block_map),
			subject: Blade::render($output->getContent()),
		);
		
		if ($output instanceof RenderedContentWithFrontMatter) {
			$event->replaceOutput(new RenderedContentWithFrontMatter(
				document: $output->getDocument(),
				content: $content,
				frontMatter: $output->getFrontMatter(),
			));
		} else {
			$event->replaceOutput(new RenderedContent(
				document: $output->getDocument(),
				content: $content,
			));
		}
	}
	
	protected function replaceCodeNodes(Node $node): void
	{
		foreach ($node->children() as $child) {
			if ($child instanceof FencedCode || $child instanceof IndentedCode) {
				$block = new HtmlBlock(HtmlBlock::TYPE_6_BLOCK_ELEMENT);
				$block->setLiteral($this->registerCodeBlock($child));
				$child->replaceWith($block);
				continue;
			}
			
			if ($child instanceof Code) {
				$child->replaceWith(new HtmlInline($this->registerCodeBlock($child)));
				continue;
			}
			
			$this->replaceCodeNodes($child);
		}
	}
	
	protected function registerCodeBlock(Node $node): string
	{
		$rendered = $this->renderCodeNode($node);
		$key = hash('sha256', $rendered);
		$this->code_block_map[$key] ??= $rendered;
		
		return "[[code:{$key}]]";
	}
	
	protected function renderCodeNode(Node $node): string
	{
		$renderer = new HtmlRenderer($this->environment);
		
		return $renderer->renderNodes([$node]);
	}
}
