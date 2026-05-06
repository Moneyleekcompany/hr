<?php

namespace App\Services\Attendance;

use App\Models\Attendance;
use App\Models\OfflinePunchSubmission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * يدمج البصمات المخزنة محليًا على هاتف الموظف (offline-buffer) في جدول attendances.
 *
 * لكل بصمة:
 *  - يُحفظ submission مرة واحدة بناءً على client_uuid (idempotent).
 *  - تُدمج البصمة في attendance لليوم: min(time) ⇒ check_in_at، max(time) ⇒ check_out_at.
 *  - لا تتراجع check_in/out إلى وقت أبكر عند الـ replay (ما عدا تصحيح طبيعي).
 */
class OfflinePunchSyncService
{
    /**
     * @param  array<int, array{
     *   client_uuid: string,
     *   punched_at: string,
     *   type: string,
     *   latitude?: float|null,
     *   longitude?: float|null,
     *   accuracy_meters?: int|null,
     *   device_id?: string|null
     * }>  $punches
     * @return array<int, array{client_uuid: string, status: string, message?: string}>
     */
    public function ingestBatch(User $user, array $punches): array
    {
        $results = [];
        foreach ($punches as $punch) {
            try {
                $results[] = $this->ingestOne($user, $punch);
            } catch (Throwable $e) {
                Log::warning('OfflinePunchSync: ingest failed', [
                    'user_id' => $user->id,
                    'client_uuid' => $punch['client_uuid'] ?? null,
                    'error' => $e->getMessage(),
                ]);
                $results[] = [
                    'client_uuid' => $punch['client_uuid'] ?? '',
                    'status' => OfflinePunchSubmission::STATUS_REJECTED,
                    'message' => $e->getMessage(),
                ];
            }
        }
        return $results;
    }

    private function ingestOne(User $user, array $punch): array
    {
        $uuid = $punch['client_uuid'];
        $existing = OfflinePunchSubmission::where('user_id', $user->id)
            ->where('client_uuid', $uuid)
            ->first();

        if ($existing) {
            return [
                'client_uuid' => $uuid,
                'status' => $existing->status,
                'message' => $existing->error_message ?: 'duplicate',
            ];
        }

        $punchedAt = Carbon::parse($punch['punched_at']);
        $type = $punch['type'] ?? OfflinePunchSubmission::TYPE_CHECK_IN;

        if (!in_array($type, [OfflinePunchSubmission::TYPE_CHECK_IN, OfflinePunchSubmission::TYPE_CHECK_OUT], true)) {
            throw new \InvalidArgumentException("Unknown punch type: {$type}");
        }

        $submission = OfflinePunchSubmission::create([
            'user_id' => $user->id,
            'client_uuid' => $uuid,
            'punched_at' => $punchedAt,
            'type' => $type,
            'latitude' => $punch['latitude'] ?? null,
            'longitude' => $punch['longitude'] ?? null,
            'accuracy_meters' => $punch['accuracy_meters'] ?? null,
            'device_id' => $punch['device_id'] ?? null,
            'status' => OfflinePunchSubmission::STATUS_PENDING,
            'raw_payload' => $punch,
        ]);

        try {
            DB::transaction(function () use ($user, $submission, $punchedAt, $type) {
                $this->applyToAttendance($user, $submission, $punchedAt, $type);
            });
            $submission->forceFill([
                'status' => OfflinePunchSubmission::STATUS_APPLIED,
                'processed_at' => now(),
            ])->save();

            return [
                'client_uuid' => $uuid,
                'status' => OfflinePunchSubmission::STATUS_APPLIED,
            ];
        } catch (Throwable $e) {
            $submission->forceFill([
                'status' => OfflinePunchSubmission::STATUS_REJECTED,
                'error_message' => mb_substr($e->getMessage(), 0, 500),
                'processed_at' => now(),
            ])->save();
            throw $e;
        }
    }

    private function applyToAttendance(
        User $user,
        OfflinePunchSubmission $submission,
        Carbon $punchedAt,
        string $type
    ): void {
        $date = $punchedAt->toDateString();
        $time = $punchedAt->format('H:i:s');

        $attendance = Attendance::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('attendance_date', $date)
            ->lockForUpdate()
            ->first();

        if (!$attendance) {
            Attendance::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'attendance_date' => $date,
                'check_in_at' => $type === OfflinePunchSubmission::TYPE_CHECK_IN ? $time : null,
                'check_out_at' => $type === OfflinePunchSubmission::TYPE_CHECK_OUT ? $time : null,
                'check_in_latitude' => $type === OfflinePunchSubmission::TYPE_CHECK_IN ? $submission->latitude : null,
                'check_in_longitude' => $type === OfflinePunchSubmission::TYPE_CHECK_IN ? $submission->longitude : null,
                'check_out_latitude' => $type === OfflinePunchSubmission::TYPE_CHECK_OUT ? $submission->latitude : null,
                'check_out_longitude' => $type === OfflinePunchSubmission::TYPE_CHECK_OUT ? $submission->longitude : null,
                'check_in_device_id' => $type === OfflinePunchSubmission::TYPE_CHECK_IN ? $submission->device_id : null,
                'check_out_device_id' => $type === OfflinePunchSubmission::TYPE_CHECK_OUT ? $submission->device_id : null,
                'check_in_accuracy_m' => $type === OfflinePunchSubmission::TYPE_CHECK_IN ? $submission->accuracy_meters : null,
                'check_out_accuracy_m' => $type === OfflinePunchSubmission::TYPE_CHECK_OUT ? $submission->accuracy_meters : null,
                'check_in_type' => $type === OfflinePunchSubmission::TYPE_CHECK_IN ? 'gps' : null,
                'check_out_type' => $type === OfflinePunchSubmission::TYPE_CHECK_OUT ? 'gps' : null,
                'attendance_status' => Attendance::ATTENDANCE_APPROVED,
                'created_by' => $user->id,
                'note' => 'offline-sync',
            ]);
            return;
        }

        $updates = [];
        if ($type === OfflinePunchSubmission::TYPE_CHECK_IN) {
            if (!$attendance->check_in_at || $time < $attendance->check_in_at) {
                $updates['check_in_at'] = $time;
                $updates['check_in_latitude'] = $submission->latitude;
                $updates['check_in_longitude'] = $submission->longitude;
                $updates['check_in_device_id'] = $submission->device_id;
                $updates['check_in_accuracy_m'] = $submission->accuracy_meters;
                $updates['check_in_type'] = 'gps';
            }
        } else { // checkOut
            $earliestCheckIn = $updates['check_in_at'] ?? $attendance->check_in_at;
            if ($earliestCheckIn && $time <= $earliestCheckIn) {
                throw new \LogicException('check-out يجب أن يكون بعد check-in.');
            }
            if (!$attendance->check_out_at || $time > $attendance->check_out_at) {
                $updates['check_out_at'] = $time;
                $updates['check_out_latitude'] = $submission->latitude;
                $updates['check_out_longitude'] = $submission->longitude;
                $updates['check_out_device_id'] = $submission->device_id;
                $updates['check_out_accuracy_m'] = $submission->accuracy_meters;
                $updates['check_out_type'] = 'gps';
            }
        }

        if (!empty($updates)) {
            $updates['edit_remark'] = 'offline-sync';
            $attendance->update($updates);
        }
    }
}
