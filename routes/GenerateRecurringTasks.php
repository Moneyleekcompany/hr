<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateRecurringTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:generate-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new tasks from active recurring tasks based on their frequency.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('بدء عملية توليد المهام المتكررة.');
        $this->info('جاري البحث عن المهام المتكررة النشطة...');

        // ملاحظة: هذا المنطق يقوم بتكرار كل المهام. يمكن تطويره مستقبلاً ليفحص التردد (يومي/أسبوعي/شهري)
        $tasks = \App\Models\Task::where('is_recurring', 1)->where('is_active', 1)->get();

        if ($tasks->isEmpty()) {
            $this->info('لم يتم العثور على مهام متكررة نشطة.');
            Log::info('لم يتم العثور على مهام متكررة نشطة.');
            return 0;
        }

        $count = 0;
        foreach ($tasks as $task) {
            $newTask = $task->replicate();
            $newTask->status = 'not_started';
            $newTask->start_date = now()->toDateString(); // يمكن تعديلها حسب التردد
            $newTask->end_date = now()->addDays(1)->toDateString(); // يمكن تعديلها حسب التردد
            $newTask->save();

            // إعادة تعيين الموظفين للمهمة الجديدة
            // الاعتماد على Eloquent لجلب المعرفات وتجنب خطأ عدم وجود الجدول
            $assignedIds = $task->assignedMembers()->pluck('member_id')->toArray();
            
            if (!empty($assignedIds)) {
                foreach ($assignedIds as $memberId) {
                    $newTask->assignedMembers()->create(['member_id' => $memberId]);
                }
            }
            
            $count++;
        }

        $this->info("تم بنجاح توليد عدد {$count} من المهام الجديدة.");
        Log::info("انتهاء عملية توليد المهام المتكررة. تم توليد {$count} مهمة.");
        return 0;
    }
}