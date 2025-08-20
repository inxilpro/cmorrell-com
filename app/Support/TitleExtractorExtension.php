<?php

namespace App\Support;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentRenderedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;

class TitleExtractorExtension implements ExtensionInterface
{
	public function register(EnvironmentBuilderInterface $environment): void
	{
		$environment->addEventListener(
			DocumentRenderedEvent::class,
			new class {
				public function __invoke(DocumentRenderedEvent $event): void
				{
					$output = $event->getOutput();
					$front_matter = $output instanceof RenderedContentWithFrontMatter ? $output->getFrontMatter() : [];
					
					if (! data_get($front_matter, 'title')) {
						data_set($front_matter, 'title', $this->findHeading($output->getDocument()));
					}
					
					$event->replaceOutput(new RenderedContentWithFrontMatter(
						document: $output->getDocument(),
						content: $output->getContent(),
						frontMatter: $front_matter
					));
				}
				
				protected function findHeading(Node $node)
				{
					if ($node instanceof Heading) {
						$text = '';
						foreach ($node->children() as $child) {
							if ($child instanceof Text) {
								$text .= $child->getLiteral();
							}
						}
						return $text ?: null;
					}
					
					foreach ($node->children() as $child) {
						if ($result = $this->findHeading($child)) {
							return $result;
						}
					}
					
					return null;
				}
			},
			-501 // One after the front matter extension
		);
	}
}
