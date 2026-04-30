<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Rats\Zkteco\Lib\ZKTeco;
use App\Models\User;
use App\Models\Attendance;
use App\Models\ZktecoDevice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class SyncZktecoAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // إعطاء وقت غير محدود للعملية في الخلفية
    public $timeout = 0;

    public function handle()
    {
        Log::info('بدء عملية سحب البصمات من الأجهزة في الخلفية.');

        try {
            $devices = ZktecoDevice::where('is_active', true)->get();

            if ($devices->isEmpty()) {
                Log::warning('لا توجد أجهزة بصمة مضافة أو مفعلة في النظام لسحب البصمات منها.');
                return;
            }

            $successCount = 0;
            $failCount = 0;

            foreach ($devices as $device) {
                try {
                    $zk = new ZKTeco($device->ip_address, $device->port ?? 4370);
                    if ($zk->connect()) {
                        $attendanceLogs = $zk->getAttendance();
                        if (!empty($attendanceLogs)) {
                            
                            // تحسين الأداء: جلب جميع الموظفين مرة واحدة لمنع استعلام قاعدة البيانات آلاف المرات (N+1 Query Problem)
                            $employeeCodes = array_unique(array_column($attendanceLogs, 'id'));
                            $users = User::whereIn('employee_code', $employeeCodes)->get()->keyBy('employee_code');

                            foreach ($attendanceLogs as $log) {
                                $user = $users->get($log['id']);
                                if ($user) {
                                    $recordTime = Carbon::parse($log['timestamp']);
                                    $date = $recordTime->format('Y-m-d');
                                    $time = $recordTime->format('H:i:s');

                                    $attendance = Attendance::where('user_id', $user->id)->where('attendance_date', $date)->first();

                                    if (!$attendance) {
                                        Attendance::create([
                                            'user_id' => $user->id,
                                            'company_id' => $user->company_id,
                                            'attendance_date' => $date,
                                            'check_in_at' => $time,
                                            'attendance_status' => 1,
                                        ]);
                                    } else {
                                        if (!$attendance->check_out_at || $time > $attendance->check_out_at) {
                                            $attendance->update(['check_out_at' => $time]);
                                        }
                                    }
                                }
                            }
                        }
                        $zk->disconnect();
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                } catch (Exception $e) {
                    $failCount++;
                }
            }
            Log::info("تم الانتهاء من سحب البصمات. نجح: $successCount, فشل: $failCount.");
        } catch (Exception $e) {
            Log::error('خطأ عام أثناء سحب البصمات في الخلفية: ' . $e->getMessage());
        }
    }
}