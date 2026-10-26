<?php

use App\Jobs\ExpireUnpaidOrdersJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new ExpireUnpaidOrdersJob)
    ->hourly()
    ->onOneServer()
    ->withoutOverlapping();
