<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Models\AttendanceEdit;
use Illuminate\Support\Facades\Auth;

class AttendanceObserver
{
    /**
     * الحقول التي يهمنا تتبع تعديلاتها لأغراض التدقيق.
     */
    private const TRACKED_FIELDS = [
        'check_in_at',
        'check_out_at',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'check_in_image',
        'check_out_image',
        'check_in_type',
        'check_out_type',
        'attendance_status',
        'attendance_date',
        'office_time_id',
        'edit_remark',
    ];

    public function created(Attendance $attendance): void
    {
        AttendanceEdit::create([
            'attendance_id' => $attendance->id,
            'edited_by' => $this->actorId(),
            'action' => AttendanceEdit::ACTION_CREATED,
            'reason' => $attendance->edit_remark,
            'ip_address' => $this->ip(),
        ]);
    }

    public function updated(Attendance $attendance): void
    {
        $changed = array_intersect(self::TRACKED_FIELDS, array_keys($attendance->getChanges()));
        if (empty($changed)) {
            return;
        }

        $rows = [];
        foreach ($changed as $field) {
            $rows[] = [
                'attendance_id' => $attendance->id,
                'edited_by' => $this->actorId(),
                'action' => AttendanceEdit::ACTION_UPDATED,
                'field' => $field,
                'old_value' => $this->stringify($attendance->getOriginal($field)),
                'new_value' => $this->stringify($attendance->getAttribute($field)),
                'reason' => $attendance->edit_remark,
                'ip_address' => $this->ip(),
                'created_at' => now(),
            ];
        }
        AttendanceEdit::insert($rows);
    }

    public function deleted(Attendance $attendance): void
    {
        AttendanceEdit::create([
            'attendance_id' => $attendance->id,
            'edited_by' => $this->actorId(),
            'action' => AttendanceEdit::ACTION_DELETED,
            'reason' => $attendance->edit_remark,
            'ip_address' => $this->ip(),
        ]);
    }

    private function actorId(): ?int
    {
        return Auth::check() ? Auth::id() : null;
    }

    private function ip(): ?string
    {
        try {
            return request()?->ip();
        } catch (\Throwable) {
            return null;
        }
    }

    private function stringify(mixed $value): ?string
    {
        if ($value === null) return null;
        if (is_scalar($value)) return (string) $value;
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
