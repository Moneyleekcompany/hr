<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfflinePunchSubmission extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPLIED = 'applied';
    public const STATUS_REJECTED = 'rejected';

    public const TYPE_CHECK_IN = 'checkIn';
    public const TYPE_CHECK_OUT = 'checkOut';

    protected $fillable = [
        'user_id',
        'client_uuid',
        'punched_at',
        'type',
        'latitude',
        'longitude',
        'accuracy_meters',
        'device_id',
        'status',
        'error_message',
        'raw_payload',
        'processed_at',
    ];

    protected $casts = [
        'punched_at' => 'datetime',
        'processed_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'accuracy_meters' => 'integer',
        'raw_payload' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
