<?php

namespace App\Services\Shift;

use App\Models\User;
use Carbon\Carbon;

class DynamicRosterService
{
    /**
     * استخراج الوردية الحالية للموظف بناءً على جدول الورديات المتغيرة (Dynamic Rostering)
     * هذا سيحل محل الدالة البسيطة `isOnNightShift`
     */
    public function getEmployeeShiftForDate(User $employee, Carbon $date)
    {
        // سيتم البحث في جدول EmployeeRoster الذي يربط الموظف بوردية معينة في يوم معين
        
        $roster = \App\Models\EmployeeRoster::where('user_id', $employee->id)->where('date', $date->toDateString())->first();
        
        if ($roster && $roster->officeTime) {
            return clone $roster->officeTime;
        }
        
        // في حال عدم وجود وردية استثنائية، يتم إرجاع الوردية الافتراضية
        return $employee->officeTime;
    }

    /**
     * تعيين أو تحديث وردية لموظف في تاريخ معين
     */
    public function assignShiftToEmployee(User $employee, $officeTimeId, Carbon $date, $isOffDay = false, $remark = null)
    {
        // استخدام updateOrCreate يمنع تكرار الورديات لنفس اليوم
        return \App\Models\EmployeeRoster::updateOrCreate(
            [
                'user_id' => $employee->id,
                'date' => $date->toDateString(),
            ],
            [
                'office_time_id' => $officeTimeId,
                'is_off_day' => $isOffDay,
                'remark' => $remark
            ]
        );
    }
}