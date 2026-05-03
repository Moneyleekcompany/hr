<?php

namespace App\Services\Attendance;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Exception;

class LocationService
{
    /**
     * التحقق من النطاق الجغرافي للموظف (Geofencing)
     */
    public function verifyGeofence(User $user, $lat, $lng, $branchLat, $branchLng)
    {
        // الموظف الميداني معفى من التحقق الجغرافي
        if ($user->user_type === 'field') {
            return true;
        }

        if (!$branchLat || !$branchLng) {
            return true; // في حال لم يتم تحديد موقع للفرع
        }

        $distance = $this->calculateDistance($lat, $lng, $branchLat, $branchLng);
        
        // جلب المسافة المسموحة من الإعدادات أو استخدام 200 متر كقيمة افتراضية
        $allowedDistance = Cache::remember('allowed_geofence_distance', 3600, function () {
            return AppSetting::where('slug', 'allowed_distance')->value('status') ?? 200;
        });

        if ($distance > $allowedDistance) {
            throw new Exception("أنت خارج نطاق العمل! المسافة بينك وبين مقر العمل هي " . round($distance) . " متر. يجب أن تكون ضمن {$allowedDistance} متر.", 403);
        }

        return true;
    }

    /**
     * حساب المسافة بين نقطتين باستخدام صيغة Haversine
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // نصف قطر الأرض بالأمتار
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}