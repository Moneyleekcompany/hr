<?php

namespace App\Services\Attendance;

use App\Models\SecurityLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    public function __construct(private SelfieExifInspector $exifInspector) {}

    /**
     * تحفظ صورة سيلفي base64، تتحقق من الوجه (AI)، وتفحص EXIF GPS مقابل الإحداثيات المُعلنة.
     *
     * @param  array{lat?: float|null, lng?: float|null}|null  $claimedLocation
     */
    public function processAndVerifyImage(
        string $base64Image,
        User $user,
        string $type,
        ?array $claimedLocation = null
    ): string {
        $imageParts = explode(';base64,', $base64Image);
        if (count($imageParts) !== 2) {
            throw new Exception('تنسيق الصورة غير صالح.', 400);
        }

        $rawBytes = base64_decode($imageParts[1], true);
        if ($rawBytes === false) {
            throw new Exception('تعذّر فك تشفير الصورة.', 400);
        }

        $this->verifyExifConsistency($user, $rawBytes, $claimedLocation, $type);

        $imageName = $type . '_' . $user->id . '_' . time() . '.png';
        $imagePath = 'uploads/attendance/' . $imageName;
        $disk = config('filesystems.default');

        Storage::disk($disk)->put($imagePath, $rawBytes);

        if (!FacialRecognitionService::verifyFace($user, $imagePath, $disk)) {
            Storage::disk($disk)->delete($imagePath);
            SecurityLog::create([
                'user_id' => $user->id,
                'type' => 'fake_face',
                'message' => 'فشل التحقق من الوجه بالذكاء الاصطناعي أثناء '
                    . ($type === 'checkin' ? 'تسجيل الحضور.' : 'تسجيل الانصراف.'),
                'ip_address' => request()?->ip(),
            ]);
            throw new Exception('فشل التحقق من الوجه بالذكاء الاصطناعي. الصورة لا تتطابق مع صورتك الشخصية المسجلة.', 403);
        }

        return $imageName;
    }

    /**
     * @param  array{lat?: float|null, lng?: float|null}|null  $claimedLocation
     */
    private function verifyExifConsistency(
        User $user,
        string $rawBytes,
        ?array $claimedLocation,
        string $type
    ): void {
        $claimedLat = $claimedLocation['lat'] ?? null;
        $claimedLng = $claimedLocation['lng'] ?? null;

        $report = $this->exifInspector->inspect($rawBytes, $claimedLat, $claimedLng);

        if (!$report['has_exif_gps']) {
            return; // كثير من الهواتف تحذف EXIF — نسمح بصمت
        }

        Log::info('Selfie EXIF inspection', array_merge(
            ['user_id' => $user->id, 'type' => $type],
            $report
        ));

        if ($report['is_consistent']) {
            return;
        }

        SecurityLog::create([
            'user_id' => $user->id,
            'type' => 'exif_mismatch',
            'message' => sprintf(
                'GPS EXIF يبعد %dم عن الموقع المُعلن (نوع: %s).',
                (int) round($report['deviation_m'] ?? 0),
                $type
            ),
            'ip_address' => request()?->ip(),
        ]);

        throw new Exception(
            'الـ GPS داخل صورة السيلفي لا يطابق موقعك المُعلن. تأكد من تفعيل الكاميرا في نفس مكان الحضور.',
            403
        );
    }
}
