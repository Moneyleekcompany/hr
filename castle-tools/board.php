<?php
/**
 * حزمة قرارات المجلس — الأرقام آليًا من القاعدة. قراءة فقط.
 *   php board.php 2026-01-01 2026-08-31
 */
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$from = $argv[1] ?? '2026-01-01';
$to   = $argv[2] ?? '2026-08-31';
$R    = [$from.' 00:00:00', $to.' 23:59:59'];

function pc($n, $d) { return $d ? round($n * 100 / $d, 1).'%' : '—'; }
function h($t) { echo "\n".str_repeat('=', 58)."\n$t\n".str_repeat('=', 58)."\n"; }
function kv($k, $v, $ref = '') { printf("  %-34s %14s %s\n", $k, $v, $ref ? "| الحزمة: $ref" : ''); }

$T = fn() => DB::table('tickets')->whereNull('deleted_at')->whereBetween('created_at', $R);

echo "\nحزمة قرارات المجلس · $from → $to\n";

// ١) الأحجام
h('١) الأحجام');
$total = $T()->count();
kv('إجمالي التذاكر', number_format($total), '33,023');
$names = DB::table('ticket_statuses')->whereNull('deleted_at')->pluck('name', 'key')->all();
foreach ($T()->select('status', DB::raw('COUNT(*) n'))->groupBy('status')->orderByDesc('n')->get() as $r) {
    kv('  '.($names[$r->status] ?? $r->status), number_format($r->n).' · '.pc($r->n, $total));
}

// ٢) الإلغاء
h('٢) الإلغاء');
$canc = $T()->whereNotNull('cancel_reason')->count();
kv('ملغاة بسبب مسجَّل', number_format($canc), '6,280');

// ٣) التسجيل المزدوج
h('٣) التسجيل المزدوج');
kv('موسوم بالنظام', number_format($T()->whereNotNull('duplicate_of_ticket_id')->count()));
$dup = DB::table('tickets as a')
    ->join('tickets as b', function ($j) {
        $j->on('a.customer_id', '=', 'b.customer_id')
          ->on('a.serial_number', '=', 'b.serial_number')
          ->on(DB::raw('DATE(a.created_at)'), '=', DB::raw('DATE(b.created_at)'))
          ->on('a.id', '!=', 'b.id');
    })
    ->whereNull('a.deleted_at')->whereNull('b.deleted_at')
    ->whereNotNull('a.cancel_reason')->whereNull('b.cancel_reason')
    ->whereNotNull('a.serial_number')->where('a.serial_number', '!=', '')
    ->whereBetween('a.created_at', $R)->distinct()->count('a.id');
kv('بقاعدة الحزمة', number_format($dup), '2,606');
kv('  نسبته من الملغاة', pc($dup, $canc), '41.5%');

// ٤) إعادة الجدولة
h('٤) إعادة الجدولة');
$rs = DB::table('ticket_reschedules')->whereNull('deleted_at')->whereBetween('created_at', $R);
kv('عمليات جدولة في الفترة', number_format((clone $rs)->count()), 'قاعدة 35,507');
kv('تذاكر متأثرة', number_format((clone $rs)->distinct()->count('ticket_id')));
kv('كل التاريخ', number_format(DB::table('ticket_reschedules')->whereNull('deleted_at')->count()));

// ٥) الالتزام بالمواعيد
h('٥) الالتزام بالمواعيد');
$base = $T()->whereNotNull('sla_resolution_deadline')->count();
if ($base) {
    $br = $T()->whereNotNull('sla_resolution_deadline')->where('sla_resolution_breached', 1)->count();
    kv('القاعدة — لها مهلة حل', number_format($base), '26,743');
    kv('ملتزم', pc($base - $br, $base), '70.3%');
    kv('متجاوز', number_format($br), '7,943');
} else { kv('الالتزام', 'لا مهل مسجَّلة'); }
$resp = $T()->whereNotNull('actual_response_minutes')->count();
kv('لها زمن استجابة', number_format($resp).' · '.pc($resp, $total));

// ٦) الإصلاح من أول مرة
h('٦) الإصلاح من أول مرة');
$fill = $T()->whereNotNull('is_first_visit_resolved')->count();
kv('الحقل مملوء', number_format($fill).' · '.pc($fill, $total), 'فارغ 100%');
if ($fill) kv('محلول من أول مرة', pc($T()->where('is_first_visit_resolved', 1)->count(), $fill), '97.5%');

// ٧) تكرار العطل
h('٧) تكرار العطل');
kv('موسوم بتكرار', number_format($T()->whereNotNull('repeat_of_ticket_id')->count()));
kv('مؤكَّد بالمراجعة', number_format($T()->where('repeat_priority_verified', 1)->count()), '1,538');
$e = DB::table('errors')->whereNull('deleted_at');
kv('أعطال مضمومة تحت جذر', number_format((clone $e)->whereNotNull('canonical_error_id')->count()), '1,111');

// ٨) الرضا
h('٨) تجربة العميل');
$s = DB::table('ticket_surveys')->whereNull('deleted_at')->whereBetween('created_at', $R);
$sn = (clone $s)->count();
kv('عدد التقييمات', number_format($sn), '6,285');
if ($sn) {
    kv('متوسط الدرجة', round((float) (clone $s)->avg('total_score'), 1), '97.2');
    kv('التغطية', pc($sn, $total), '25.2%');
    foreach ((clone $s)->select('satisfaction_level', DB::raw('COUNT(*) c'))->groupBy('satisfaction_level')->orderByDesc('c')->get() as $d) {
        kv('  '.($d->satisfaction_level ?: '—'), number_format($d->c).' · '.pc($d->c, $sn));
    }
}

// ٩) الشبكة
h('٩) الشبكة');
$asg = $T()->whereNotNull('center_id')->count();
if ($asg) {
    $top = $T()->whereNotNull('center_id')->select('center_id', DB::raw('COUNT(*) n'))
        ->groupBy('center_id')->orderByDesc('n')->limit(10)->get();
    $cn = DB::table('users')->whereIn('id', $top->pluck('center_id'))->pluck('name', 'id')->all();
    foreach ($top as $r) kv('  '.($cn[$r->center_id] ?? '#'.$r->center_id), number_format($r->n).' · '.pc($r->n, $asg));
    kv('تركّز أعلى عشرة', pc($top->sum('n'), $asg), '47.9%');
}
kv('حسابات المراكز', number_format(DB::table('users')->whereNull('deleted_at')->where('type', 'center')->count()), '114 / 97 عامل');

// ١٠) المال
h('١٠) التحصيل والتكلفة');
$c = DB::table('collections')->whereNull('deleted_at')->whereBetween('created_at', $R);
kv('مطلوب تحصيله', number_format((float) (clone $c)->sum('amount'), 2).' ج', '2,251,945');
kv('محصَّل فعلًا', number_format((float) (clone $c)->where('is_collected', 1)->sum('amount'), 2).' ج');

echo "\n  المسيّرات:\n";
foreach (DB::table('hr_payroll_runs')->whereNull('deleted_at')->orderBy('period')->get() as $r) {
    kv('  '.$r->period.' ('.$r->status.')', number_format((float) $r->total_net, 2).' ج · '.$r->employees_count.' موظف');
}

$x = DB::table('expenses')->whereNull('deleted_at')
    ->whereBetween('expense_month', [substr($from, 0, 7), substr($to, 0, 7)]);
echo "\n  المصروفات:\n";
foreach ((clone $x)->select('type', DB::raw('SUM(amount) s'), DB::raw('COUNT(*) c'))->groupBy('type')->orderByDesc('s')->get() as $r) {
    kv('  '.($r->type ?: '— بلا بند —'), number_format((float) $r->s, 2).' ج ('.$r->c.')');
}
kv('إجمالي المصروفات', number_format((float) (clone $x)->sum('amount'), 2).' ج', '1,370,479');

echo "\n".str_repeat('=', 58)."\n";
echo "غير متاح اليوم:\n";
echo "  * إهلاك السيارات والعدة (520,000 ج) — لا جدول أصول\n";
echo "  * تكلفة البلاغ (202.8 ج) — لا تُصدَر قبل اكتمال المرتبات والمصروفات والإهلاك\n\n";
