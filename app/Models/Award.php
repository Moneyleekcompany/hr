<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Award extends Model
{
    use HasFactory;

    protected $table = 'awards';
    protected $fillable = [
        'employee_id', 'award_type_id', 'gift_item', 'award_base', 'awarded_date', 'awarded_by', 'status', 'award_description', 'gift_description', 'attachment', 'reward_code',
    ];


    const RECORDS_PER_PAGE = 20;

    const UPLOAD_PATH = 'uploads/award/';

    public function type(): BelongsTo
    {
        return $this->belongsTo(AwardType::class,'award_type_id','id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class,'employee_id','id');
    }

}
