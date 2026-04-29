<?php

namespace App\Observers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;

class AppSettingObserver
{
    public function saved(AppSetting $appSetting)
    {
        // إذا تم تعديل أو حفظ إعداد التعرف على الوجه، نقوم بمسحه من الكاش ليتجدد تلقائياً
        if ($appSetting->slug === 'facial-recognition') {
            Cache::forget('facial_recognition_status');
        }
    }
}