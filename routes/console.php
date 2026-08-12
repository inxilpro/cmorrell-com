<?php

use App\Console\Commands\DownloadsStatsCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command(DownloadsStatsCommand::class)->dailyAt('07:00');
