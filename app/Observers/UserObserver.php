<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Task;
use App\Models\Asset;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserObserver implements ShouldQueue
{
    /**
     * يتم التنفيذ تلقائياً عند إنشاء موظف جديد (Onboarding)
     */
    public function created(User $user)
    {
        try {
            // 1. إرسال بريد إلكتروني ترحيبي للموظف
            // نفترض وجود كلاس البريد، ويجب أن يُرسل في الخلفية
            // Mail::to($user->email)->queue(new \App\Mail\WelcomeEmployeeMail($user));

            // 2. إنشاء مهام تعريفية (Onboarding Tasks) للموظف الجديد تلقائياً
            Task::create([
                'name' => 'قراءة سياسة الشركة ودليل الموظف',
                'project_id' => null, // مهمة عامة
                'status' => 'not_started',
                'start_date' => now(),
                'end_date' => now()->addDays(3),
            ]);
            
            // هنا يمكن إضافة الموظف للمهمة برمجياً
            // \App\Models\TaskMember::create(['task_id' => $task->id, 'user_id' => $user->id]);

            // 3. توجيه إشعار لقسم الـ IT وإدارة العمليات لتجهيز المعدات
            Log::info("إرسال إشعار لقسم الـ IT لتجهيز معدات الموظف: {$user->name}");
            
            Log::info("تم تنفيذ خطوات الـ Onboarding للموظف: {$user->name}");
        } catch (\Exception $e) {
            Log::error("خطأ في أتمتة الانضمام: " . $e->getMessage());
        }
    }

    /**
     * يتم التنفيذ عند تحديث الموظف (تحديداً مراقبة المغادرة Offboarding)
     */
    public function updated(User $user)
    {
        // التحقق مما إذا كانت حالة الموظف تغيرت إلى "غير نشط" (إنهاء خدمات)
        if ($user->isDirty('is_active') && $user->is_active == 0) {
            try {
                // 1. توليد طلب إخلاء طرف للعهد (Assets Clearance)
                $assets = Asset::where('assigned_to', $user->id)->get();
                if ($assets->count() > 0) {
                    Log::info("يوجد {$assets->count()} عهدة مسجلة على الموظف {$user->name} يجب إخلاؤها.");
                    // إنشاء تنبيه للإدارة بوجود عهد لم تُسترد
                    \App\Models\Notification::create([
                        'title' => 'طلب إخلاء طرف للعهد',
                        'description' => "الموظف {$user->name} تم إنهاء خدماته ويوجد لديه {$assets->count()} عهدة يجب استردادها.",
                        'type' => 'alert'
                    ]);
                }

                // 2. إيقاف حسابه من أجهزة البصمة تلقائياً
                // \App\Jobs\RemoveZktecoUserJob::dispatch($user->employee_code);

                Log::info("تم تنفيذ خطوات الـ Offboarding للموظف: {$user->name}");
            } catch (\Exception $e) {
                Log::error("خطأ في أتمتة المغادرة: " . $e->getMessage());
            }
        }
    }
}