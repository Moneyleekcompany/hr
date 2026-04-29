<?php

namespace App\Console;

use App\Models\Notification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            Notification::query()
                ->whereNotNull('notification_publish_date')
                ->whereDate('created_at', '<=', now()->subDays(90))
                ->delete();
        })->daily();

        $schedule->command('command:holiday-notification')
            ->dailyAt('07:00');

        $schedule->command('command:birthday-notification')
            ->dailyAt('07:05');

        $schedule->command('command:award-notification')
            ->dailyAt('07:10');

        // جدولة سحب البصمات من الأجهزة مرتين يومياً (12:00 ظهراً و 12:00 منتصف الليل)
        $schedule->command('zkteco:sync')->twiceDaily(0, 12);

        // أتمتة الرواتب: إنشاء مسودة الرواتب شهرياً يوم 25 الساعة 8 صباحاً
        $schedule->command('payroll:generate-monthly')->monthlyOn(25, '08:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
