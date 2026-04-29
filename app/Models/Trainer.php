<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trainer extends Model
{
    use HasFactory;
    protected $table = 'trainers';
    protected $fillable = [
        'trainer_type', 'branch_id', 'department_id', 'employee_id', 'name', 'contact_number', 'email', 'expertise', 'address', 'created_by', 'updated_by','status'
    ];


    const RECORDS_PER_PAGE = 20;


    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class,'employee_id','id');
    }
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class,'branch_id','id');
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class,'department_id','id');
    }
}
