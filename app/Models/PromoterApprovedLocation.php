<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoterApprovedLocation extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'latitude',
        'longitude',
        'radius_meters',
        'is_active',
        'valid_from',
        'valid_until',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meters' => 'integer',
        'is_active' => 'boolean',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCurrentlyValid(\DateTimeInterface $when = null): bool
    {
        $when = $when ?: now();
        if (!$this->is_active) return false;
        if ($this->valid_from && $when < $this->valid_from) return false;
        if ($this->valid_until && $when > $this->valid_until->endOfDay()) return false;
        return true;
    }
}
