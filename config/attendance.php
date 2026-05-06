<?php

return [
    'face_recognition' => [
        // 'aws' | 'null' (passthrough — يقبل أي وجه)
        'driver' => env('FACE_RECOGNITION_DRIVER', 'null'),

        // الحد الأدنى للتشابه ٪ لاعتبار الوجه مطابقًا
        'threshold' => (float) env('FACE_RECOGNITION_THRESHOLD', 85.0),

        // مهلة طلب AWS Rekognition بالثواني
        'timeout' => (int) env('FACE_RECOGNITION_TIMEOUT', 8),

        'aws' => [
            'region' => env('FACE_RECOGNITION_AWS_REGION', env('AWS_DEFAULT_REGION', 'us-east-1')),
            'key' => env('FACE_RECOGNITION_AWS_KEY', env('AWS_ACCESS_KEY_ID')),
            'secret' => env('FACE_RECOGNITION_AWS_SECRET', env('AWS_SECRET_ACCESS_KEY')),
        ],
    ],

    'gps' => [
        // نصف القطر الافتراضي بالأمتار للـ geofencing حول الفرع
        'default_radius_meters' => (int) env('ATTENDANCE_GEOFENCE_RADIUS', 200),

        // أقصى سرعة منطقية بين نقطتي check-in/check-out (كم/س) — أعلى من ذلك يعتبر مشبوه
        'max_plausible_speed_kmh' => (int) env('ATTENDANCE_MAX_SPEED_KMH', 150),

        // أقصى انحراف بالأمتار بين GPS الـ EXIF للسيلفي والإحداثيات المُعلنة
        'exif_max_deviation_m' => (int) env('ATTENDANCE_EXIF_MAX_DEVIATION_M', 500),
    ],

    'zkteco' => [
        // عدد محاولات إعادة الاتصال بالجهاز قبل اعتباره offline
        'sync_retries' => (int) env('ZKTECO_SYNC_RETRIES', 3),

        // عتبة اعتبار الجهاز offline (دقائق منذ آخر مزامنة ناجحة)
        'offline_threshold_minutes' => (int) env('ZKTECO_OFFLINE_THRESHOLD_MIN', 120),

        // كم يوم نسحب من سجل الجهاز عند أول مزامنة (delta sync بعدها)
        'initial_sync_days' => (int) env('ZKTECO_INITIAL_SYNC_DAYS', 7),
    ],

    'audit' => [
        // الاحتفاظ بسجل تعديلات الحضور لكم يوم
        'retain_edits_days' => (int) env('ATTENDANCE_AUDIT_RETAIN_DAYS', 365),
    ],
];
