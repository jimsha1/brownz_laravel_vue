<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->everyMinute();

Schedule::call(function () {
    Artisan::call('attendance:check');
})->everySecond();

Schedule::call(function () {
    Artisan::call('app:check-booking-flag-command');
})->everySecond();
