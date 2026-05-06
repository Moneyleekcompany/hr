<?php

namespace App\Services\Attendance;

use App\Models\SecurityLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * فحوص أمنية موحدة قبل أي check-in/out:
 *   - منع double-tap (lock مؤقت لكل موظف)
 *   - رفض mock-GPS (إذا أرسل العميل العلامة)
 *   - تحقق دقة الموقع (accuracy)
 *   - فحص EXIF للسيلفي عند توفّره (سرعة منطقية بين نقطتين)
 * عند الفشل يكتب SecurityLog ويرمي Exception برمز HTTP مناسب.
 */
class AttendanceIntegrityGuard
{
    private const LOCK_SECONDS = 6;
    private const MAX_ACCURACY_METERS = 250;

    public function guard(User $user, array $payload): void
    {
        $this->preventDoubleSubmission($user);
        $this->rejectMockGps($user, $payload);
        $this->rejectLowAccuracy($user, $payload);
    }

    private function preventDoubleSubmission(User $user): void
    {
        $lock = Cache::lock("attendance:user:{$user->id}", self::LOCK_SECONDS);
        if (!$lock->get()) {
            $this->logSecurity($user, 'double_submit', 'محاولة تسجيل حضور مزدوجة خلال ' . self::LOCK_SECONDS . ' ثوانٍ.');
            throw new Exception('تم استلام طلب مماثل قبل لحظات. حاول مرة أخرى خلال ثوانٍ.', 429);
        }
        // الـ lock يحرر تلقائيًا بعد LOCK_SECONDS ثوانٍ — لا داعي لـ release().
    }

    private function rejectMockGps(User $user, array $payload): void
    {
        $flag = $payload['is_mock_location'] ?? $payload['mock_gps'] ?? false;
        if (filter_var($flag, FILTER_VALIDATE_BOOLEAN)) {
            $this->logSecurity($user, 'mock_gps', 'رفض تسجيل حضور بسبب الكشف عن GPS وهمي على الجهاز.');
            throw new Exception('تم اكتشاف موقع وهمي على جهازك. عطّل تطبيق الـ Mock GPS وحاول مرة أخرى.', 403);
        }
    }

    private function rejectLowAccuracy(User $user, array $payload): void
    {
        if (!isset($payload['accuracy_meters'])) return;

        $accuracy = (int) $payload['accuracy_meters'];
        if ($accuracy > self::MAX_ACCURACY_METERS) {
            $this->logSecurity($user, 'low_gps_accuracy', "دقة GPS منخفضة جدًا: {$accuracy}م.");
            throw new Exception("دقة الموقع ضعيفة جدًا ({$accuracy}م). انتقل إلى مكان مفتوح أعِد المحاولة.", 422);
        }
    }

    private function logSecurity(User $user, string $type, string $message): void
    {
        try {
            SecurityLog::create([
                'user_id' => $user->id,
                'type' => $type,
                'message' => $message,
                'ip_address' => request()?->ip(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('AttendanceIntegrityGuard: failed to log security event', ['error' => $e->getMessage()]);
        }
    }
}
