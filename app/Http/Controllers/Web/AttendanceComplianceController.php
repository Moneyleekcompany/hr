<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceComplianceController extends Controller
{
    public function index(Request $request)
    {
        $monthInput = $request->input('month', now()->format('Y-m'));
        try {
            $start = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
        } catch (\Throwable) {
            $start = now()->startOfMonth();
        }
        $end = (clone $start)->endOfMonth();
        if ($end->isFuture()) $end = now();

        $branchId = $request->filled('branch_id') ? (int) $request->input('branch_id') : null;

        $branches = Branch::where('is_active', 1)->orderBy('name')->get(['id', 'name']);

        $usersQuery = User::query()
            ->where('is_active', 1)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $totalEmployees = (clone $usersQuery)->count();
        $userIds = (clone $usersQuery)->pluck('id')->all();

        // أيام العمل في الشهر (نستثني الجمعة/السبت كافتراض، ويمكن لاحقًا قراءة الإعداد من DB)
        $workdaysInMonth = collect(CarbonPeriod::create($start, $end))
            ->filter(fn (Carbon $d) => !in_array($d->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY], true))
            ->count();

        // ملخص الحضور لكل موظف
        $perUser = Attendance::withoutGlobalScopes()
            ->select(
                'user_id',
                DB::raw('COUNT(*) as present_days'),
                DB::raw('SUM(CASE WHEN check_out_at IS NULL THEN 1 ELSE 0 END) as missing_checkouts'),
                DB::raw('SUM(COALESCE(overtime, 0)) as total_overtime_min'),
                DB::raw('SUM(COALESCE(undertime, 0)) as total_undertime_min')
            )
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->when(!empty($userIds), fn ($q) => $q->whereIn('user_id', $userIds))
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $employees = (clone $usersQuery)
            ->select('id', 'name', 'branch_id', 'department_id')
            ->with(['branch:id,name'])
            ->get()
            ->map(function (User $u) use ($perUser, $workdaysInMonth) {
                $row = $perUser->get($u->id);
                $present = (int) ($row->present_days ?? 0);
                $missingCheckouts = (int) ($row->missing_checkouts ?? 0);
                $absent = max(0, $workdaysInMonth - $present);
                $rate = $workdaysInMonth > 0 ? round($present * 100 / $workdaysInMonth, 1) : 0;
                return (object) [
                    'id' => $u->id,
                    'name' => $u->name,
                    'branch' => $u->branch?->name,
                    'present_days' => $present,
                    'absent_days' => $absent,
                    'missing_checkouts' => $missingCheckouts,
                    'overtime_minutes' => (int) ($row->total_overtime_min ?? 0),
                    'undertime_minutes' => (int) ($row->total_undertime_min ?? 0),
                    'compliance_rate' => $rate,
                ];
            });

        $summary = [
            'total_employees' => $totalEmployees,
            'workdays_in_month' => $workdaysInMonth,
            'expected_attendance' => $totalEmployees * $workdaysInMonth,
            'recorded_attendance' => $employees->sum('present_days'),
            'total_absences' => $employees->sum('absent_days'),
            'total_missing_checkouts' => $employees->sum('missing_checkouts'),
            'total_overtime_hours' => round($employees->sum('overtime_minutes') / 60, 1),
            'total_undertime_hours' => round($employees->sum('undertime_minutes') / 60, 1),
            'company_compliance_rate' => $totalEmployees > 0 && $workdaysInMonth > 0
                ? round($employees->sum('present_days') * 100 / ($totalEmployees * $workdaysInMonth), 1)
                : 0,
        ];

        // Top 5 الأكثر غيابًا و الأكثر التزامًا
        $topAbsent = $employees->sortByDesc('absent_days')->take(5)->values();
        $topCompliant = $employees->where('present_days', '>', 0)->sortByDesc('compliance_rate')->take(5)->values();

        // ملخص لكل فرع
        $byBranch = $employees->groupBy('branch')->map(function ($rows, $branch) use ($workdaysInMonth) {
            $present = $rows->sum('present_days');
            $expected = $rows->count() * $workdaysInMonth;
            return (object) [
                'branch' => $branch ?: '—',
                'employees' => $rows->count(),
                'present_days' => $present,
                'absent_days' => $rows->sum('absent_days'),
                'compliance_rate' => $expected > 0 ? round($present * 100 / $expected, 1) : 0,
            ];
        })->values();

        return view('admin.attendance.compliance', [
            'monthInput' => $start->format('Y-m'),
            'start' => $start,
            'end' => $end,
            'branchId' => $branchId,
            'branches' => $branches,
            'summary' => $summary,
            'employees' => $employees->sortByDesc('absent_days')->values(),
            'topAbsent' => $topAbsent,
            'topCompliant' => $topCompliant,
            'byBranch' => $byBranch,
        ]);
    }
}
