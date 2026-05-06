<?php

namespace App\Console\Commands;

use App\Helpers\AppHelper;
use App\Models\Attendance;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class RemindMissingCheckoutCommand extends Command
{
    protected $signature = 'attendance:remind-checkout {--hours=8} {--silent}';

    protected $description = 'يذكّر الموظفين الذين سجّلوا حضورهم اليوم ولم يسجّلوا انصرافهم بعد X ساعات';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $today = now()->toDateString();
        $cutoffTime = now()->subHours($hours)->format('H:i:s');

        // أهداف: حضور اليوم، check_out_at = null، check_in_at قبل أكثر من X ساعات
        $candidates = Attendance::withoutGlobalScopes()
            ->where('attendance_date', $today)
            ->whereNull('check_out_at')
            ->where('check_in_at', '<=', $cutoffTime)
            ->with('employee:id,name,department_id')
            ->get();

        $sent = 0;
        foreach ($candidates as $attendance) {
            if (!$attendance->employee) continue;

            $cacheKey = "checkout_reminder:{$attendance->id}";
            if (Cache::has($cacheKey)) {
                continue; // أُرسل تذكير مسبقًا اليوم
            }
            Cache::put($cacheKey, true, Carbon::tomorrow()->startOfDay());

            $name = $attendance->employee->name;
            try {
                AppHelper::sendNotificationToAuthorizedUser(
                    'تذكير: لم يتم تسجيل الانصراف',
                    "الموظف {$name} سجّل حضوره ولم يسجّل انصرافه حتى الآن (مر {$hours} ساعة).",
                    'employee_check_out'
                );
                $sent++;
            } catch (\Throwable $e) {
                // نتجاهل لكي لا نوقف الباقي
            }
        }

        if (!$this->option('silent')) {
            $this->info("Reminded {$sent} of {$candidates->count()} candidates.");
        }

        return self::SUCCESS;
    }
}
