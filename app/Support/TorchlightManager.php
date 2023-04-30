<?php

namespace App\Support;

use Torchlight\Manager;

class TorchlightManager extends Manager
{
	public function processFileContents($file)
	{
		$files = collect($this->config('snippet_directories', []))
			->map(fn($dir) => str($dir)->finish(DIRECTORY_SEPARATOR)->append($file))
			->prepend($file);
		
		foreach ($files as $file) {
			if (@is_readable($file)) {
				return file_get_contents($file);
			}
		}
		
		return false;
	}
}
