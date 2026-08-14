<?php

namespace App\Console\Commands;

use App\Http\Downloads;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PostDeployCommand extends Command
{
	protected $signature = 'postdeploy';
	
	public function handle(): void
	{
		Cache::forget('posts:all');
		
		$this->info('Cleared per-deploy cache items!');
	}
}
