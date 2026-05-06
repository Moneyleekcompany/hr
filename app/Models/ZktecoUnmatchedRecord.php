<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZktecoUnmatchedRecord extends Model
{
    protected $fillable = [
        'device_id',
        'employee_code',
        'punched_at',
        'raw_payload',
        'resolved_user_id',
        'resolved_at',
    ];

    protected $casts = [
        'punched_at' => 'datetime',
        'resolved_at' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(ZktecoDevice::class, 'device_id');
    }

    public function resolvedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_user_id');
    }
}
