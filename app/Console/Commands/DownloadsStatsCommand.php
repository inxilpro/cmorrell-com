<?php

namespace App\Console\Commands;

use App\Http\Downloads;
use Illuminate\Console\Command;

class DownloadsStatsCommand extends Command
{
	protected $signature = 'downloads:stats';

	public function handle(Downloads $downloads): void
	{
		$downloads->setLogger(fn($message) => $this->info($message));
		$downloads();

		$this->table(['Repository', 'Package', 'Downloads'], $downloads->data);
		$this->newLine();
		$this->line('Total downloads: <info>'.number_format($downloads->total).'</info>');
	}
}
