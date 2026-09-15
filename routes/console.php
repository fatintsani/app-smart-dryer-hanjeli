<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Scalable Database Telemetry Downsampling / Aggregation Cron Job.
 * Runs nightly at 02:00 AM to aggregate raw 5-second telemetry readings
 * into 5-minute averaged buckets for all completed batches.
 */
Schedule::command('greenhouse:downsample-telemetry --interval=5')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->runInBackground();

