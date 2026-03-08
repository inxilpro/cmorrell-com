<?php

namespace App\Console\Commands;

use App\Http\Downloads;
use Illuminate\Console\Command;

class DownloadsStatsCommand extends Command
{
	protected $signature = 'downloads:stats {--refresh-zeroes} {--refresh-all}';
	
	public function handle(Downloads $downloads): void
	{
		$downloads->setLogger(fn($message) => $this->info($message));
		
		$downloads->refresh_zeroes = $this->option('refresh-zeroes');
		$downloads->refresh_all = $this->option('refresh-all');
		
		$downloads();
		
		$this->table(['Repository', 'Package', 'Downloads'], $downloads->data);
		$this->newLine();
		$this->line('Total downloads: <info>'.number_format($downloads->total).'</info>');
	}
}
