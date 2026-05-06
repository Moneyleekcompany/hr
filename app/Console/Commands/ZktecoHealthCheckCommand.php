<?php

namespace App\Console\Commands;

use App\Models\ZktecoDevice;
use App\Models\ZktecoUnmatchedRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ZktecoHealthCheckCommand extends Command
{
    protected $signature = 'zkteco:health-check {--silent}';

    protected $description = 'يفحص حالة أجهزة ZKTeco، يكتشف الـ offline منها، ويلخّص البصمات غير المطابقة';

    public function handle(): int
    {
        $thresholdMinutes = (int) config('attendance.zkteco.offline_threshold_minutes', 120);
        $threshold = now()->subMinutes($thresholdMinutes);

        $devices = ZktecoDevice::where('is_active', true)->get();
        $offline = $devices->filter(fn (ZktecoDevice $d) => $d->last_synced_at && $d->last_synced_at->lt($threshold));
        $stale = $devices->filter(fn (ZktecoDevice $d) => $d->last_run_status === ZktecoDevice::STATUS_FAILED);

        $unmatchedCount = ZktecoUnmatchedRecord::whereNull('resolved_at')->count();

        if (!$this->option('silent')) {
            $this->line("Active devices: {$devices->count()}");
            $this->line("Offline (no sync > {$thresholdMinutes}m): {$offline->count()}");
            $this->line("Last run failed: {$stale->count()}");
            $this->line("Unmatched punches pending resolution: {$unmatchedCount}");
        }

        if ($offline->isNotEmpty() || $stale->isNotEmpty()) {
            Log::warning('ZKTeco health check: degraded devices', [
                'offline' => $offline->map(fn ($d) => [
                    'id' => $d->id,
                    'name' => $d->name,
                    'ip' => $d->ip_address,
                    'last_synced_at' => optional($d->last_synced_at)->toDateTimeString(),
                    'consecutive_failures' => $d->consecutive_failures,
                ])->values(),
                'stale_failed' => $stale->map(fn ($d) => [
                    'id' => $d->id,
                    'name' => $d->name,
                    'last_error' => $d->last_error_message,
                ])->values(),
                'unmatched_pending' => $unmatchedCount,
            ]);
        }

        return self::SUCCESS;
    }
}
