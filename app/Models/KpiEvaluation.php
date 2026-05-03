<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiEvaluation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'evaluator_id', 'month', 'year', 'attendance_score', 'task_score', 'direct_manager_score', 'total_score', 'feedback'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}