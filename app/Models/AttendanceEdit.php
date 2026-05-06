<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceEdit extends Model
{
    public const UPDATED_AT = null;

    public const ACTION_CREATED = 'created';
    public const ACTION_UPDATED = 'updated';
    public const ACTION_DELETED = 'deleted';

    protected $fillable = [
        'attendance_id',
        'edited_by',
        'action',
        'field',
        'old_value',
        'new_value',
        'reason',
        'ip_address',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by', 'id');
    }
}
