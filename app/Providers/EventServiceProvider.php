<?php

namespace App\Providers;

use App\Models\User;
use App\Models\AppSetting;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Observers\UserObserver;
use App\Observers\AppSettingObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // تسجيل المراقب الخاص بالموظفين لتفعيل أتمتة الانضمام والمغادرة
        User::observe(UserObserver::class);

        // تسجيل المراقب الخاص بإعدادات النظام لتفريغ الكاش عند التعديل
        AppSetting::observe(AppSettingObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
