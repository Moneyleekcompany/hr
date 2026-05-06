<?php

namespace App\Services\Attendance;

use App\Models\AppSetting;
use App\Models\PromoterApprovedLocation;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Exception;

class LocationService
{
    /**
     * يتحقق من النطاق الجغرافي للموظف بناءً على نوعه:
     *  - مكتبي (office)/فرع: ضمن نصف قطر معد (افتراضي 200م) من الفرع.
     *  - ميداني (field)/بروموتر: ضمن نصف قطر أحد المواقع المعتمدة (promoter_approved_locations).
     *
     * يعيد ['ok' => bool, 'matched_location' => PromoterApprovedLocation|null] للحالات الميدانية،
     * أو يرمي Exception عند الرفض.
     */
    public function verifyGeofence(User $user, $lat, $lng, $branchLat = null, $branchLng = null): array
    {
        if ($user->user_type === 'field') {
            return $this->verifyFieldEmployee($user, $lat, $lng);
        }

        return $this->verifyOfficeEmployee($lat, $lng, $branchLat, $branchLng);
    }

    private function verifyFieldEmployee(User $user, $lat, $lng): array
    {
        if ($lat === null || $lng === null) {
            throw new Exception('يجب تفعيل الـ GPS لتسجيل الحضور الميداني.', 422);
        }

        $locations = PromoterApprovedLocation::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        if ($locations->isEmpty()) {
            // لا توجد مواقع معتمدة لهذا الموظف الميداني — نرفض بدلاً من الإعفاء الصامت
            throw new Exception('لا توجد مواقع عمل معتمدة على حسابك. تواصل مع HR.', 403);
        }

        $now = now();
        $best = null;
        $bestDistance = null;

        foreach ($locations as $loc) {
            if (!$loc->isCurrentlyValid($now)) continue;
            $d = $this->calculateDistance($lat, $lng, $loc->latitude, $loc->longitude);
            if ($d <= $loc->radius_meters && ($bestDistance === null || $d < $bestDistance)) {
                $best = $loc;
                $bestDistance = $d;
            }
        }

        if ($best === null) {
            throw new Exception('أنت خارج نطاق أي موقع عمل معتمد.', 403);
        }

        return ['ok' => true, 'matched_location' => $best, 'distance' => $bestDistance];
    }

    private function verifyOfficeEmployee($lat, $lng, $branchLat, $branchLng): array
    {
        if (!$branchLat || !$branchLng) {
            return ['ok' => true, 'matched_location' => null, 'distance' => null];
        }

        if ($lat === null || $lng === null) {
            return ['ok' => true, 'matched_location' => null, 'distance' => null];
        }

        $distance = $this->calculateDistance($lat, $lng, $branchLat, $branchLng);
        $allowedDistance = $this->allowedDistance();

        if ($distance > $allowedDistance) {
            throw new Exception(
                "أنت خارج نطاق العمل! المسافة بينك وبين مقر العمل هي " . round($distance) . " متر. يجب أن تكون ضمن {$allowedDistance} متر.",
                403
            );
        }

        return ['ok' => true, 'matched_location' => null, 'distance' => $distance];
    }

    private function allowedDistance(): int
    {
        return (int) Cache::remember('allowed_geofence_distance', 3600, function () {
            $setting = AppSetting::where('slug', 'allowed_distance')->value('status');
            return (int) ($setting ?: config('attendance.gps.default_radius_meters', 200));
        });
    }

    /**
     * Haversine بالأمتار.
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
           * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
