<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Payroll\GeneratePayrollService;
use App\Models\User;
use App\Helpers\WhatsAppHelper;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyPayrollCommand extends Command
{
    protected $signature = 'payroll:generate-monthly';
    protected $description = 'أتمتة حساب وإنشاء مسودة الرواتب الشهرية يوم 25 من كل شهر';

    public function handle(GeneratePayrollService $payrollService)
    {
        Log::info('بدء عملية أتمتة إنشاء الرواتب الشهرية...');

        try {
            $month = date('m');
            $year = date('Y');

            // البيانات الأساسية لإنشاء الرواتب
            $filterData = [
                'year' => $year,
                'month' => $month,
                'include_tada' => 1,
                'include_advance_salary' => 1,
                'attendance' => 1
            ];

            // خدمة توليد الرواتب الموجودة مسبقاً (تقوم بجمع التأخيرات، الإضافي، إلخ)
            $payrollService->getEmployeeSalariesToCreatePayslip($filterData);

            // إرسال إشعار لمدير الموارد البشرية (صلاحيات الـ Admin)
            $hrManagers = User::role('admin')->get(); // أو حسب الصلاحية لديك
            foreach ($hrManagers as $manager) {
                if ($manager->phone) {
                    $message = "🤖 *النظام الآلي - إدارة الرواتب*\n\nمرحباً {$manager->name}،\nتم الانتهاء من تجهيز *مسودة مسيرات الرواتب* لشهر {$month}/{$year} بنجاح ✅.\n\nيرجى الدخول للوحة التحكم للمراجعة والاعتماد.";
                    WhatsAppHelper::sendMessage($manager->phone, $message);
                }
            }

            $this->info('تم إنشاء مسودة الرواتب وإرسال الإشعارات بنجاح!');
        } catch (\Exception $e) {
            Log::error('فشل في أتمتة الرواتب: ' . $e->getMessage());
            $this->error('حدث خطأ أثناء أتمتة الرواتب.');
        }
    }
}