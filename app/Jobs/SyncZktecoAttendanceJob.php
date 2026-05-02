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

    // تحديد حد أقصى للعملية لتجنب تعليق الـ Queue worker للأبد في حال عدم استجابة الجهاز (5 دقائق مثلاً)
    public $timeout = 300;

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
                            
                            // تجميع تواريخ البصمات التي تم سحبها للبحث عنها مرة واحدة
                            $dates = array_unique(array_map(function($log) {
                                return Carbon::parse($log['timestamp'])->format('Y-m-d');
                            }, $attendanceLogs));
                            
                            // جلب سجلات الحضور الموجودة مسبقاً في الذاكرة لتخفيف الضغط عن الـ Database
                            $existingAttendances = Attendance::whereIn('user_id', $users->pluck('id'))
                                ->whereIn('attendance_date', $dates)
                                ->get()
                                ->groupBy(function($item) {
                                    return $item->user_id . '_' . $item->attendance_date;
                                });

                            foreach ($attendanceLogs as $log) {
                                $user = $users->get($log['id']);
                                if ($user) {
                                    $recordTime = Carbon::parse($log['timestamp']);
                                    $date = $recordTime->format('Y-m-d');
                                    $time = $recordTime->format('H:i:s');
                                    $cacheKey = $user->id . '_' . $date;

                                    $attendance = isset($existingAttendances[$cacheKey]) ? $existingAttendances[$cacheKey]->first() : null;

                                    if (!$attendance) {
                                        $attendance = Attendance::create([
                                            'user_id' => $user->id,
                                            'company_id' => $user->company_id,
                                            'attendance_date' => $date,
                                            'check_in_at' => $time,
                                            'attendance_status' => 1,
                                        ]);
                                        // إضافته للذاكرة لتجنب إنشائه مرة أخرى في نفس اللوب
                                        $existingAttendances[$cacheKey] = collect([$attendance]);
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