<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\User;
use App\Models\ZktecoDevice;
use App\Models\ZktecoUnmatchedRecord;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Rats\Zkteco\Lib\ZKTeco;
use Throwable;

class SyncZktecoAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** أقصى زمن للوظيفة (5 دقائق). */
    public int $timeout = 300;

    /** عدد محاولات إعادة المحاولة على مستوى الـ Queue. */
    public int $tries = 3;

    /** تأخير المحاولات على مستوى الـ Queue (بالثواني). */
    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function handle(): void
    {
        Log::info('ZKTeco sync started');

        $devices = ZktecoDevice::where('is_active', true)->get();
        if ($devices->isEmpty()) {
            Log::warning('ZKTeco sync: no active devices');
            return;
        }

        $totalSuccess = 0;
        $totalFail = 0;

        foreach ($devices as $device) {
            try {
                $this->syncDevice($device);
                $totalSuccess++;
            } catch (Throwable $e) {
                $totalFail++;
                $this->markDeviceFailure($device, $e->getMessage());
                Log::error('ZKTeco sync: device failed', [
                    'device_id' => $device->id,
                    'name' => $device->name,
                    'ip' => $device->ip_address,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('ZKTeco sync finished', [
            'success' => $totalSuccess,
            'failed' => $totalFail,
        ]);
    }

    private function syncDevice(ZktecoDevice $device): void
    {
        $zk = $this->connectWithRetry($device);
        try {
            $logs = $zk->getAttendance() ?: [];
        } finally {
            try { $zk->disconnect(); } catch (Throwable) {}
        }

        if (empty($logs)) {
            $this->markDeviceSuccess($device, null);
            return;
        }

        // Delta sync: تجاهل البصمات الأقدم من آخر بصمة معروفة لهذا الجهاز
        $cutoff = $device->last_punch_at;
        $newLogs = [];
        $maxPunchAt = $cutoff;

        foreach ($logs as $log) {
            try {
                $ts = Carbon::parse($log['timestamp']);
            } catch (Throwable) {
                continue;
            }
            if ($cutoff && $ts->lte($cutoff)) {
                continue;
            }
            $newLogs[] = ['ts' => $ts, 'employee_code' => (string) ($log['id'] ?? ''), 'raw' => $log];
            if (!$maxPunchAt || $ts->gt($maxPunchAt)) {
                $maxPunchAt = $ts;
            }
        }

        if (empty($newLogs)) {
            $this->markDeviceSuccess($device, $maxPunchAt);
            return;
        }

        $employeeCodes = array_values(array_unique(array_filter(array_column($newLogs, 'employee_code'))));
        $users = User::whereIn('employee_code', $employeeCodes)->get()->keyBy('employee_code');

        // (employee_code => [date => [punches]])
        $grouped = [];
        $unmatched = [];
        foreach ($newLogs as $entry) {
            $code = $entry['employee_code'];
            if ($code === '' || !$users->has($code)) {
                $unmatched[] = $entry;
                continue;
            }
            $date = $entry['ts']->format('Y-m-d');
            $grouped[$code][$date][] = $entry['ts'];
        }

        DB::transaction(function () use ($grouped, $users, $device) {
            foreach ($grouped as $code => $byDate) {
                $user = $users->get($code);
                foreach ($byDate as $date => $timestamps) {
                    sort($timestamps);
                    $first = $timestamps[0];
                    $last = end($timestamps);

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
                            'check_in_at' => $first->format('H:i:s'),
                            'check_out_at' => $last->ne($first) ? $last->format('H:i:s') : null,
                            'attendance_status' => Attendance::ATTENDANCE_APPROVED,
                            'check_in_type' => 'fingerprint',
                            'check_out_type' => $last->ne($first) ? 'fingerprint' : null,
                            'created_by' => $user->id,
                        ]);
                        continue;
                    }

                    $updates = [];
                    if (!$attendance->check_in_at || $first->format('H:i:s') < $attendance->check_in_at) {
                        $updates['check_in_at'] = $first->format('H:i:s');
                        $updates['check_in_type'] = 'fingerprint';
                    }
                    $newCheckOut = $last->format('H:i:s');
                    if ((!$attendance->check_out_at || $newCheckOut > $attendance->check_out_at)
                        && $newCheckOut > ($updates['check_in_at'] ?? $attendance->check_in_at)) {
                        $updates['check_out_at'] = $newCheckOut;
                        $updates['check_out_type'] = 'fingerprint';
                    }
                    if (!empty($updates)) {
                        $attendance->update($updates);
                    }
                }
            }
        });

        $this->persistUnmatched($device, $unmatched);
        $this->markDeviceSuccess($device, $maxPunchAt);
    }

    private function connectWithRetry(ZktecoDevice $device): ZKTeco
    {
        $attempts = max(1, (int) config('attendance.zkteco.sync_retries', 3));
        $lastError = null;

        for ($i = 1; $i <= $attempts; $i++) {
            try {
                $zk = new ZKTeco($device->ip_address, $device->port ?? 4370);
                if ($zk->connect()) {
                    return $zk;
                }
                $lastError = 'connect() returned false';
            } catch (Throwable $e) {
                $lastError = $e->getMessage();
            }
            if ($i < $attempts) {
                sleep(min(15, $i * 5));
            }
        }

        throw new \RuntimeException("Failed to connect to {$device->ip_address}:{$device->port}: {$lastError}");
    }

    /**
     * @param  list<array{ts: Carbon, employee_code: string, raw: array}>  $unmatched
     */
    private function persistUnmatched(ZktecoDevice $device, array $unmatched): void
    {
        if (empty($unmatched)) return;

        $rows = [];
        foreach ($unmatched as $entry) {
            $rows[] = [
                'device_id' => $device->id,
                'employee_code' => $entry['employee_code'] ?: 'unknown',
                'punched_at' => $entry['ts']->toDateTimeString(),
                'raw_payload' => json_encode($entry['raw'], JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // upsert على المفتاح الفريد (device_id, employee_code, punched_at) لتفادي التكرار
        ZktecoUnmatchedRecord::query()->upsert(
            $rows,
            ['device_id', 'employee_code', 'punched_at'],
            ['raw_payload', 'updated_at']
        );
    }

    private function markDeviceSuccess(ZktecoDevice $device, ?Carbon $maxPunchAt): void
    {
        $device->forceFill([
            'last_synced_at' => now(),
            'last_run_status' => ZktecoDevice::STATUS_OK,
            'last_error_message' => null,
            'consecutive_failures' => 0,
            'last_punch_at' => $maxPunchAt ?? $device->last_punch_at,
        ])->save();
    }

    private function markDeviceFailure(ZktecoDevice $device, string $error): void
    {
        $device->forceFill([
            'last_run_status' => ZktecoDevice::STATUS_FAILED,
            'last_error_message' => mb_substr($error, 0, 1000),
            'consecutive_failures' => (int) $device->consecutive_failures + 1,
        ])->save();
    }
}
