<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Models\Ride;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    Ride::where('status', 'scheduled')
        ->where('scheduled_time', '<=', now())
        ->update(['status' => 'pending']);
})->everyMinute();
