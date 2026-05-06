<?php

namespace App\Services\Attendance;

use App\Models\AppSetting;
use App\Models\User;
use App\Services\Attendance\FaceRecognition\FaceComparatorFactory;
use App\Services\Attendance\FaceRecognition\FaceComparisonException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FacialRecognitionService
{
    /**
     * يطابق صورة الـ check-in/out المرفوعة مع صورة الموظف الشخصية.
     *
     * @param  User    $user      الموظف
     * @param  string  $candidateRelativePath  المسار النسبي للصورة على الـ disk
     * @param  string|null  $disk            الـ disk المستخدم (افتراضي: filesystems.default)
     * @return bool                           true لو متطابقة، false لو لا
     */
    public static function verifyFace(User $user, string $candidateRelativePath, ?string $disk = null): bool
    {
        if (!self::isEnabled()) {
            return true;
        }

        if (!$user->avatar) {
            return true;
        }

        $disk = $disk ?: config('filesystems.default');

        try {
            $candidateBytes = Storage::disk($disk)->get($candidateRelativePath);
        } catch (Throwable $e) {
            Log::error('Face verification: candidate image read failed', [
                'user_id' => $user->id,
                'path' => $candidateRelativePath,
                'disk' => $disk,
                'error' => $e->getMessage(),
            ]);
            return false;
        }

        $referenceBytes = self::loadReferenceImage($user);
        if ($referenceBytes === null) {
            Log::warning('Face verification: reference avatar missing', [
                'user_id' => $user->id,
                'avatar' => $user->avatar,
            ]);
            return false;
        }

        try {
            $comparator = FaceComparatorFactory::make();
            $similarity = $comparator->compare($referenceBytes, $candidateBytes);
        } catch (FaceComparisonException $e) {
            Log::error('Face verification: comparator failure', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            // فشل تقني (شبكة/خدمة) ≠ تزييف. السلوك الآمن: نرفض حتى يتم الفحص يدويًا.
            return false;
        }

        $threshold = (float) config('attendance.face_recognition.threshold', 85.0);
        $passed = $similarity >= $threshold;

        Log::info('Face verification result', [
            'user_id' => $user->id,
            'similarity' => $similarity,
            'threshold' => $threshold,
            'passed' => $passed,
        ]);

        return $passed;
    }

    public static function isEnabled(): bool
    {
        $driver = config('attendance.face_recognition.driver', 'null');
        if ($driver === 'null') {
            return false;
        }

        return (bool) Cache::remember('facial_recognition_status', 3600, function () {
            return AppSetting::where('slug', 'facial-recognition')->value('status');
        });
    }

    private static function loadReferenceImage(User $user): ?string
    {
        $localPath = public_path(User::AVATAR_UPLOAD_PATH . $user->avatar);
        if (is_file($localPath)) {
            $bytes = @file_get_contents($localPath);
            return $bytes !== false ? $bytes : null;
        }

        // يدعم تخزين السيرفرات السحابية إذا تم نقل المستودع
        $disk = config('filesystems.default');
        $remotePath = User::AVATAR_UPLOAD_PATH . $user->avatar;
        if (Storage::disk($disk)->exists($remotePath)) {
            try {
                return Storage::disk($disk)->get($remotePath);
            } catch (Throwable $e) {
                return null;
            }
        }

        return null;
    }
}
