<?php

namespace App\Console\Commands;

use App\Models\AttendanceEdit;
use App\Models\SecurityLog;
use App\Models\ZktecoUnmatchedRecord;
use Illuminate\Console\Command;

class PruneAttendanceAuditCommand extends Command
{
    protected $signature = 'attendance:prune-audit
        {--days= : عدد الأيام للاحتفاظ — يتجاوز config(attendance.audit.retain_edits_days)}
        {--dry-run : يعرض العدد دون حذف}
        {--silent}';

    protected $description = 'يحذف سجلات تدقيق الحضور القديمة وفقًا لسياسة الاحتفاظ';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('attendance.audit.retain_edits_days', 365));
        if ($days < 30) {
            $this->error('لا يمكن الاحتفاظ بأقل من 30 يومًا.');
            return self::FAILURE;
        }

        $cutoff = now()->subDays($days);
        $dryRun = (bool) $this->option('dry-run');
        $silent = (bool) $this->option('silent');

        $tasks = [
            'attendance_edits' => fn () => AttendanceEdit::where('created_at', '<', $cutoff),
            'security_logs' => fn () => SecurityLog::where('created_at', '<', $cutoff),
            'zkteco_unmatched_records (resolved)' => fn () =>
                ZktecoUnmatchedRecord::whereNotNull('resolved_at')
                    ->where('resolved_at', '<', $cutoff),
        ];

        $totalDeleted = 0;
        foreach ($tasks as $label => $queryFn) {
            $query = $queryFn();
            $count = $query->count();
            if ($count === 0) {
                if (!$silent) $this->line("  {$label}: لا يوجد ما يُحذف");
                continue;
            }
            if ($dryRun) {
                if (!$silent) $this->line("  {$label}: سيُحذف {$count} سجل (dry-run)");
                continue;
            }
            $deleted = $query->delete();
            $totalDeleted += $deleted;
            if (!$silent) $this->line("  {$label}: حُذف {$deleted}");
        }

        if (!$silent) {
            $this->info("Pruning complete. Cutoff: {$cutoff->toDateTimeString()}, total deleted: {$totalDeleted}.");
        }
        return self::SUCCESS;
    }
}
