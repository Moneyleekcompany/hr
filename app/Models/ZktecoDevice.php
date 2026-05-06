<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZktecoDevice extends Model
{
    use HasFactory;

    public const STATUS_OK = 'ok';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'name',
        'ip_address',
        'port',
        'branch_id',
        'is_active',
        'last_synced_at',
        'last_punch_at',
        'last_run_status',
        'last_error_message',
        'consecutive_failures',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
        'last_punch_at' => 'datetime',
        'consecutive_failures' => 'integer',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function isOffline(): bool
    {
        if (!$this->last_synced_at) {
            return false; // لم يبدأ التزامن بعد، لا نعتبره offline
        }
        $threshold = (int) config('attendance.zkteco.offline_threshold_minutes', 120);
        return $this->last_synced_at->lt(now()->subMinutes($threshold));
    }
}
