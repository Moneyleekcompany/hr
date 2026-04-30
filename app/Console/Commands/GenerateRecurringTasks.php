<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateRecurringTasks extends Command
{
    protected $signature = 'tasks:generate-recurring';
    protected $description = 'إنشاء المهام الدورية المتكررة تلقائياً';

    public function handle()
    {
        Log::info('بدء إنشاء المهام الدورية (الرد على الرسائل، متابعة المبيعات)...');
        
        try {
            $recurringTasks = Task::with('assignedMembers')->where('is_recurring', 1)->get();
            
            foreach ($recurringTasks as $task) {
                $shouldCreate = false;
                
                if ($task->recurring_frequency == 'daily') {
                    $shouldCreate = true;
                } elseif ($task->recurring_frequency == 'weekly' && now()->isMonday()) {
                    $shouldCreate = true;
                } elseif ($task->recurring_frequency == 'monthly' && now()->day == 1) {
                    $shouldCreate = true;
                }
                
                if ($shouldCreate) {
                    $newTask = $task->replicate();
                    $newTask->start_date = now()->format('Y-m-d');
                    $newTask->end_date = now()->format('Y-m-d');
                    $newTask->status = 'not_started';
                    $newTask->is_recurring = 0; // لكي لا يتم استنساخ النسخة المنشأة مرة أخرى
                    $newTask->save();
                    
                    // نسخ وتعيين نفس الموظفين المكلفين بالمهمة الأم
                    foreach ($task->assignedMembers as $member) {
                        $newTask->assignedMembers()->create(['member_id' => $member->member_id]);
                    }
                }
            }
            $this->info('تم إنشاء المهام الدورية بنجاح!');
        } catch (\Exception $e) {
            Log::error('خطأ في توليد المهام الدورية: ' . $e->getMessage());
        }
    }
}