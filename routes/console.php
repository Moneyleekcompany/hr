<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('zkteco:sync', function () {
    $this->info('Starting ZKTeco Attendance Sync...');
    try {
        $controller = app(\App\Http\Controllers\Web\ZKTecoController::class);
        $controller->syncAttendance();
        $this->info('ZKTeco Attendance Synced Successfully!');
    } catch (\Exception $e) {
        $this->error('Failed to sync: ' . $e->getMessage());
    }
})->purpose('Sync attendance from ZKTeco biometric device');
