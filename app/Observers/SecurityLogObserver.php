<?php

namespace App\Observers;

use App\Helpers\AppHelper;
use App\Models\SecurityLog;
use Illuminate\Support\Facades\Cache;

class SecurityLogObserver
{
    /**
     * أنواع الحوادث التي تستدعي إشعار المدراء فورًا.
     */
    private const ALERT_TYPES = [
        'mock_gps',
        'Fake GPS',
        'تزييف الوجه (Fake Face)',
        'fake_face',
        'low_gps_accuracy',
        'exif_mismatch',
    ];

    private const THROTTLE_MINUTES = 5;
    private const PERMISSION_KEY = 'view_security_alert';

    public function created(SecurityLog $log): void
    {
        if (!in_array($log->type, self::ALERT_TYPES, true)) {
            return;
        }

        $cacheKey = "security_alert:{$log->user_id}:{$log->type}";
        if (Cache::has($cacheKey)) {
            return;
        }
        Cache::put($cacheKey, true, now()->addMinutes(self::THROTTLE_MINUTES));

        try {
            AppHelper::sendNotificationToAuthorizedUser(
                'تنبيه أمني — حضور مشبوه',
                "تم رصد حدث ({$log->type}) للمستخدم #{$log->user_id}: {$log->message}",
                self::PERMISSION_KEY
            );
        } catch (\Throwable) {
            // لا نوقف التدفق الأصلي عند فشل الإشعار
        }
    }
}
