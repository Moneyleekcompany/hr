<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRoster extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'office_time_id', 'date', 'is_off_day', 'remark'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function officeTime()
    {
        return $this->belongsTo(OfficeTime::class);
    }
}