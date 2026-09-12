<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * حزمة قرارات المجلس — الأرقام آليًا من القاعدة.
 *
 * منهجية تقرير «كاسيل — ما بعد البيع · يناير–أغسطس ٢٠٢٦»:
 * كل نسبة مكتوب معها عدد الحالات وقاعدتها، ولا يُقارَن رقم بغير قاعدته.
 *
 * قراءة فقط — لا يكتب صفًا واحدًا.
 *
 *   php artisan board:report --from=2026-01-01 --to=2026-08-31
 *   php artisan board:report --from=... --to=... --json
 */
class BoardReport extends Command
{
    protected $signature = 'board:report
        {--from= : بداية الفترة YYYY-MM-DD}
        {--to=   : نهاية الفترة YYYY-MM-DD}
        {--json  : مخرَج JSON إضافي}';

    protected $description = 'أرقام لوحة المجلس من قاعدة البيانات — قراءة فقط';

    private string $from;
    private string $to;
    private array $json = [];
    private array $gaps = [];

    public function handle(): int
    {
        $this->from = $this->option('from') ?: now()->startOfYear()->toDateString();
        $this->to   = $this->option('to')   ?: now()->toDateString();

        $this->line('');
        $this->info("حزمة قرارات المجلس · {$this->from} → {$this->to}");
        $this->line(str_repeat('=', 64));

        $this->volumes();
        $this->cancellations();
        $this->duplicates();
        $this->reschedules();
        $this->sla();
        $this->firstVisit();
        $this->repeats();
        $this->satisfaction();
        $this->network();
        $this->geography();
        $this->money();
        $this->kpis();

        $this->gapReport();

        if ($this->option('json')) {
            $this->line('');
            $this->line(json_encode(
                ['period' => [$this->from, $this->to], 'metrics' => $this->json, 'gaps' => $this->gaps],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ));
        }

        return self::SUCCESS;
    }

    // ── ١) الأحجام ومطابقة الأعداد (صفحة ٣) ────────────────────────
    private function volumes(): void
    {
        $total = $this->t()->count();
        $this->h('١) الأحجام ومطابقة الأعداد');

        $names = DB::table('ticket_statuses')->whereNull('deleted_at')->pluck('name', 'key')->all();

        $rows = $this->t()->select('status', DB::raw('COUNT(*) n'))
            ->groupBy('status')->orderByDesc('n')->get();

        $body = [];
        $sum  = 0;
        foreach ($rows as $r) {
            $sum += $r->n;
            $body[] = [$names[$r->status] ?? (string) $r->status, number_format($r->n), $this->pc($r->n, $total)];
        }
        $body[] = ['— المجموع —', number_format($sum), $this->pc($sum, $total)];

        $this->table(['الحالة', 'العدد', '% من الإجمالي'], $body);
        $this->kv('إجمالي التذاكر في الفترة', number_format($total));
        $this->json['tickets_total'] = $total;

        if ($sum !== $total) {
            $this->warnLine("المجموع ({$sum}) لا يساوي الإجمالي ({$total}) — صفوف بحالة فارغة.");
        }

        // بلاغات العملاء مقابل ما ليس عميلًا — الأساس الذي تُقرأ عليه الجودة
        $this->line('');
        $byType = $this->t()->select('type', DB::raw('COUNT(*) n'))
            ->groupBy('type')->orderByDesc('n')->get();
        $this->table(['نوع الخدمة', 'تذاكر', '%'], $byType->map(fn ($r) => [
            (string) ($r->type ?: '— بلا نوع —'), number_format($r->n), $this->pc($r->n, $total),
        ])->all());
        $this->note('مؤشرات جودة الخدمة تُقرأ على بلاغات العملاء وحدها — مرتجعات الورشة والتجار والتركيب تُستبعَد (صفحة ٣).');
    }

    // ── ٢) الإلغاء ─────────────────────────────────────────────────
    private function cancellations(): void
    {
        $this->h('٢) الإلغاء');

        $cancelled = $this->t()->whereNotNull('cancel_reason');
        $n = (clone $cancelled)->count();
        $this->kv('تذاكر لها سبب إلغاء مسجَّل', number_format($n));
        $this->json['cancelled'] = $n;

        if (! $n) { $this->gap('توزيع أسباب الإلغاء', 'لا تذاكر بسبب إلغاء في الفترة'); return; }

        $rows = (clone $cancelled)->select('cancel_reason', DB::raw('COUNT(*) c'))
            ->groupBy('cancel_reason')->orderByDesc('c')->get();
        $this->table(['السبب', 'العدد', '%'], $rows->map(fn ($r) => [
            (string) $r->cancel_reason, number_format($r->c), $this->pc($r->c, $n),
        ])->all());

        if (Schema::hasColumn('tickets', 'cancel_reason_is_derived')) {
            $d = (clone $cancelled)->where('cancel_reason_is_derived', 1)->count();
            $this->kv('منها مستنتَج رجعيًا (موسوم)', number_format($d) . ' — ' . $this->pc($d, $n));
        }
    }

    // ── ٣) التسجيل المزدوج (صفحة ٦) ────────────────────────────────
    private function duplicates(): void
    {
        $this->h('٣) التسجيل المزدوج');

        $flagged = $this->t()->whereNotNull('duplicate_of_ticket_id')->count();
        $this->kv('موسوم بالنظام (duplicate_of_ticket_id)', number_format($flagged));
        $this->json['duplicates_flagged'] = $flagged;

        // القاعدة المعتمدة في الحزمة: ملغاة يقابلها منجَز لنفس العميل ونفس السيريال في نفس اليوم
        $dup = DB::table('tickets as a')
            ->join('tickets as b', function ($j) {
                $j->on('a.customer_id', '=', 'b.customer_id')
                  ->on('a.serial_number', '=', 'b.serial_number')
                  ->on(DB::raw('DATE(a.created_at)'), '=', DB::raw('DATE(b.created_at)'))
                  ->on('a.id', '!=', 'b.id');
            })
            ->whereNull('a.deleted_at')->whereNull('b.deleted_at')
            ->whereNotNull('a.cancel_reason')
            ->whereNull('b.cancel_reason')
            ->whereNotNull('a.serial_number')->where('a.serial_number', '!=', '')
            ->whereBetween('a.created_at', $this->range())
            ->distinct()->count('a.id');

        $this->kv('بقاعدة الحزمة (عميل + سيريال + نفس اليوم)', number_format($dup));
        $this->json['duplicates_rule'] = $dup;
        $this->note('الحزمة: 2,606 من 6,280 ملغاة = 41.5٪ تسجيل مزدوج لا عمل ضائع.');

        if (Schema::hasColumn('tickets', 'duplicate_check_result')) {
            $on = $this->t()->whereNotNull('duplicate_check_result')->count();
            $this->kv('مرّ عليه الفحص الآلي عند الفتح', number_format($on));
            if (! $on) $this->gap('الفحص الآلي للتكرار', 'duplicate_check_result فارغ — الفحص غير مفعَّل عند الفتح');
        }
    }

    // ── ٤) إعادة الجدولة ───────────────────────────────────────────
    private function reschedules(): void
    {
        $this->h('٤) إعادة الجدولة');

        $ops = DB::table('ticket_reschedules')->whereNull('deleted_at')
            ->whereBetween('created_at', $this->range())->count();
        $tix = DB::table('ticket_reschedules')->whereNull('deleted_at')
            ->whereBetween('created_at', $this->range())->distinct()->count('ticket_id');
        $all = DB::table('ticket_reschedules')->whereNull('deleted_at')->count();

        $this->table(['البند', 'القيمة'], [
            ['عمليات الجدولة في الفترة', number_format($ops)],
            ['تذاكر متأثرة',             number_format($tix)],
            ['كل التاريخ',               number_format($all)],
        ]);
        $this->json['reschedule_ops'] = $ops;

        $this->note('القاعدة عمليات جدولة لا تذاكر — التذكرة قد تُجدوَل أكثر من مرة (صفحة ٣).');
        $this->warnLine('الحزمة تذكر قاعدة 35,507 عملية. الجدول كله ' . number_format($all)
            . ' صف — المصدران مختلفان، يُحسم قبل اعتماد المؤشر.');
    }

    // ── ٥) الالتزام بالمواعيد والـSLA ──────────────────────────────
    private function sla(): void
    {
        $this->h('٥) الالتزام بالمواعيد');

        $withDeadline = $this->t()->whereNotNull('sla_resolution_deadline');
        $base = (clone $withDeadline)->count();
        if (! $base) { $this->gap('الالتزام بالمواعيد', 'لا تذاكر لها sla_resolution_deadline في الفترة'); return; }

        $breached = (clone $withDeadline)->where('sla_resolution_breached', 1)->count();
        $ok = $base - $breached;

        $this->table(['البند', 'العدد', '%'], [
            ['القاعدة — لها مهلة حل', number_format($base), '100%'],
            ['ملتزم',                 number_format($ok),      $this->pc($ok, $base)],
            ['متجاوز',                number_format($breached), $this->pc($breached, $base)],
        ]);
        $this->json['sla_resolution_compliance'] = round($ok * 100 / $base, 1);
        $this->note('الحزمة: 70.3٪ التزام · 7,943 متجاوزة من 26,743 · المستهدف 95٪ والالتزام بعد 90 يومًا 86٪.');

        $resp = $this->t()->whereNotNull('actual_response_minutes')->count();
        $totalT = $this->t()->count();
        $this->kv('لها زمن استجابة مسجَّل', number_format($resp) . ' — ' . $this->pc($resp, $totalT));
        if ($totalT && $resp * 100 / $totalT < 90) {
            $this->gap('زمن الاستجابة', 'ناقص في ' . $this->pc($totalT - $resp, $totalT) . ' من التذاكر');
        }
    }

    // ── ٦) الإصلاح من أول مرة ──────────────────────────────────────
    private function firstVisit(): void
    {
        $this->h('٦) الإصلاح من أول مرة');

        if (! Schema::hasColumn('tickets', 'is_first_visit_resolved')) {
            $this->gap('الإصلاح من أول مرة', 'العمود is_first_visit_resolved غير موجود');
            return;
        }
        $total  = $this->t()->count();
        $filled = $this->t()->whereNotNull('is_first_visit_resolved')->count();
        $yes    = $this->t()->where('is_first_visit_resolved', 1)->count();

        $this->table(['البند', 'العدد', '%'], [
            ['تذاكر الفترة',   number_format($total),  '100%'],
            ['الحقل مملوء',    number_format($filled), $this->pc($filled, $total)],
            ['محلول من أول مرة', number_format($yes),  $filled ? $this->pc($yes, $filled) : '—'],
        ]);
        $this->json['first_visit_fill_rate'] = $total ? round($filled * 100 / $total, 1) : null;

        if (! $filled) {
            $this->gap('الإصلاح من أول مرة (97.5٪)',
                'الحقل فارغ في 100٪ من التذاكر — الرقم يُحسب يدويًا كل شهر. ضبطه عند الإقفال يجعله آليًا.');
        } elseif ($total && $filled * 100 / $total < 80) {
            $this->gap('الإصلاح من أول مرة', 'الحقل مملوء في ' . $this->pc($filled, $total) . ' فقط — المؤشر غير ممثِّل');
        }
    }

    // ── ٧) تكرار العطل ─────────────────────────────────────────────
    private function repeats(): void
    {
        $this->h('٧) تكرار العطل');

        $flagged  = $this->t()->whereNotNull('repeat_of_ticket_id')->count();
        $verified = $this->t()->where('repeat_priority_verified', 1)->count();

        $this->table(['البند', 'العدد'], [
            ['موسوم بتكرار',  number_format($flagged)],
            ['مؤكَّد بالمراجعة', number_format($verified)],
        ]);
        $this->json['repeat_verified'] = $verified;
        $this->note('«تكرار عطل» لا يُعرض إلا عند verified = 1 — الوسم غير المراجَع لا يُصدَّق (§٤-ج).');

        $dupErr = DB::table('errors')->whereNull('deleted_at')->whereNotNull('canonical_error_id')->count();
        $canon  = DB::table('errors')->whereNull('deleted_at')->where('is_canonical', 1)->count();
        $this->kv('أعطال مضمومة تحت جذر', number_format($dupErr) . ' من ' . number_format(DB::table('errors')->whereNull('deleted_at')->count()));
        $this->kv('أعطال جذرية', number_format($canon));
    }

    // ── ٨) الرضا ───────────────────────────────────────────────────
    private function satisfaction(): void
    {
        $this->h('٨) تجربة العميل');

        $q = DB::table('ticket_surveys')->whereNull('deleted_at')->whereBetween('created_at', $this->range());
        $n = (clone $q)->count();
        if (! $n) { $this->gap('الرضا', 'لا تقييمات في الفترة'); return; }

        $avg = (float) (clone $q)->avg('total_score');
        $this->kv('عدد التقييمات', number_format($n));
        $this->kv('متوسط الدرجة', round($avg, 1));
        $this->kv('التغطية من تذاكر الفترة', $this->pc($n, $this->t()->count()));
        $this->json['surveys'] = ['n' => $n, 'avg' => round($avg, 1)];

        $dist = (clone $q)->select('satisfaction_level', DB::raw('COUNT(*) c'))
            ->groupBy('satisfaction_level')->orderByDesc('c')->get();
        $this->table(['الفئة', 'العدد', '%'], $dist->map(fn ($r) => [
            (string) ($r->satisfaction_level ?: '—'), number_format($r->c), $this->pc($r->c, $n),
        ])->all());

        $top = $dist->first();
        if ($top && $n && $top->c * 100 / $n > 90) {
            $this->note('التوزيع مشبَّع (' . $this->pc($top->c, $n) . ' في خانة واحدة) — المؤشر إنذار للحالات السالبة لا ترتيب للمراكز (صفحة ١١).');
        }
    }

    // ── ٩) الشبكة ──────────────────────────────────────────────────
    private function network(): void
    {
        $this->h('٩) الشبكة');

        $assigned = $this->t()->whereNotNull('center_id')->count();
        if (! $assigned) { $this->gap('تركّز الشبكة', 'لا بلاغات مسنَدة لمركز'); return; }

        $top = $this->t()->whereNotNull('center_id')
            ->select('center_id', DB::raw('COUNT(*) n'))
            ->groupBy('center_id')->orderByDesc('n')->limit(10)->get();

        $names = DB::table('users')->whereIn('id', $top->pluck('center_id'))->pluck('name', 'id')->all();

        $this->table(['المركز', 'بلاغات', '% من المسنَد'], $top->map(fn ($r) => [
            $names[$r->center_id] ?? ('#' . $r->center_id), number_format($r->n), $this->pc($r->n, $assigned),
        ])->all());

        $share = round($top->sum('n') * 100 / $assigned, 1);
        $this->kv('تركّز أعلى عشرة', $share . '%  (الحزمة 47.9٪ · المستهدف ٤٠٪)');
        $this->json['top10_share'] = $share;

        $active = DB::table('users')->whereNull('deleted_at')->where('type', 'center')->count();
        $this->kv('حسابات المراكز', number_format($active));
    }

    // ── ١٠) الجغرافيا ──────────────────────────────────────────────
    private function geography(): void
    {
        $this->h('١٠) خريطة الطلب');

        if (! Schema::hasTable('addresses')) { $this->gap('خريطة الطلب', 'جدول addresses غير موجود'); return; }
        $cityCol = null;
        foreach (['city_id', 'governorate_id', 'area_id'] as $c) {
            if (Schema::hasColumn('addresses', $c)) { $cityCol = $c; break; }
        }
        if (! $cityCol) { $this->gap('خريطة الطلب', 'لا عمود مدينة/محافظة في addresses'); return; }

        $rows = DB::table('tickets as t')
            ->join('addresses as a', 'a.id', '=', 't.address_id')
            ->whereNull('t.deleted_at')->whereBetween('t.created_at', $this->range())
            ->select("a.{$cityCol} as loc", DB::raw('COUNT(*) n'))
            ->groupBy("a.{$cityCol}")->orderByDesc('n')->limit(12)->get();

        $names = Schema::hasTable('cities') && $cityCol === 'city_id'
            ? DB::table('cities')->pluck('name', 'id')->all() : [];

        $tot = $this->t()->count();
        $this->table(['الموقع', 'بلاغات', '%'], $rows->map(fn ($r) => [
            $names[$r->loc] ?? ('#' . $r->loc), number_format($r->n), $this->pc($r->n, $tot),
        ])->all());
    }

    // ── ١١) المال ──────────────────────────────────────────────────
    private function money(): void
    {
        $this->h('١١) التحصيل والتكلفة');

        // التحصيلات
        $col = DB::table('collections')->whereNull('deleted_at')->whereBetween('created_at', $this->range());
        $this->table(['البند', 'القيمة'], [
            ['مطلوب تحصيله', number_format((float) (clone $col)->sum('amount'), 2) . ' ج'],
            ['محصَّل فعلًا',  number_format((float) (clone $col)->where('is_collected', 1)->sum('amount'), 2) . ' ج'],
            ['ضريبة',        number_format((float) (clone $col)->sum('vat_amount'), 2) . ' ج'],
        ]);

        // المرتبات
        $this->line('');
        $runs = DB::table('hr_payroll_runs')->whereNull('deleted_at')
            ->orderBy('period')->get(['period', 'status', 'employees_count', 'total_gross', 'total_net']);
        if ($runs->count()) {
            $this->table(['المسيّر', 'الحالة', 'موظفون', 'إجمالي', 'صافي'], $runs->map(fn ($r) => [
                (string) $r->period, (string) $r->status, (string) $r->employees_count,
                number_format((float) $r->total_gross, 2), number_format((float) $r->total_net, 2),
            ])->all());
            $this->note('فارق الصرف شهر واحد: المسيّر المعنون بشهر P يحمل بلاغات P−١ (§٤-هـ).');
        }
        $months = $this->monthsInRange();
        if ($runs->count() < $months) {
            $this->gap('المرتبات للفترة كاملة',
                "الفترة {$months} شهرًا ولها {$runs->count()} مسيّر فقط — الباقي بلا مسيّر مقيَّد في النظام");
        }

        // المصروفات
        $this->line('');
        $ex = DB::table('expenses')->whereNull('deleted_at');
        if (Schema::hasColumn('expenses', 'expense_month')) {
            $ex->whereBetween('expense_month', [substr($this->from, 0, 7), substr($this->to, 0, 7)]);
        }
        $byType = (clone $ex)->select('type', DB::raw('SUM(amount) s'), DB::raw('COUNT(*) c'))
            ->groupBy('type')->orderByDesc('s')->get();
        $sum = (float) (clone $ex)->sum('amount');

        if ($byType->count()) {
            $this->table(['بند المصروف', 'صفوف', 'القيمة'], $byType->map(fn ($r) => [
                (string) ($r->type ?: '— بلا بند —'), (string) $r->c, number_format((float) $r->s, 2),
            ])->all());
        }
        $this->kv('إجمالي المصروفات المسجَّلة في الفترة', number_format($sum, 2) . ' ج');
        $this->json['expenses_total'] = $sum;

        $this->note('بنود الحزمة السبعة (صفحة ٢١): سولار وكارتات 584,598 · فروع الصيانة 264,598 · '
            . 'سيستم المكالمات 71,875 · سكن الموظفين 29,333 · شحن القطع 140,557 · الرقم المختصر 106,667 · '
            . 'مشاريب 62,400 = 1,370,479.');

        if ($sum < 1000000) {
            $this->gap('المصروفات التشغيلية (1,370,479 ج)',
                'المسجَّل ' . number_format($sum, 2) . ' ج فقط — البنود السبعة تحتاج إدخالًا شهريًا بنوعها');
        }
        $this->gap('إهلاك السيارات والعدة (520,000 ج)',
            'لا جدول أصول — الأساس قيمة الإحلال ÷ العمر المتبقي، لا القيمة الدفترية');
        $this->gap('تكلفة البلاغ (202.8 ج)',
            'لا تُصدَر قبل اكتمال المرتبات والمصروفات والإهلاك — لا تُقدَّر');
    }

    // ── ١٢) المؤشرات ───────────────────────────────────────────────
    private function kpis(): void
    {
        if (! Schema::hasTable('kpi_definitions')) return;
        $this->h('١٢) مؤشرات الأداء');

        $this->table(['الجدول', 'صفوف'], [
            ['kpi_definitions', number_format(DB::table('kpi_definitions')->count())],
            ['kpi_targets',     number_format(DB::table('kpi_targets')->count())],
            ['kpi_actuals',     number_format(DB::table('kpi_actuals')->count())],
        ]);
        $this->note('مجموع أوزان كل فئة لازم = ١٠٠٪ بالضبط، وإلا خرج التقييم ناقصًا بلا أن يظهر (§٤-ز).');
    }

    // ── أدوات ──────────────────────────────────────────────────────
    private function t()
    {
        return DB::table('tickets')->whereNull('deleted_at')->whereBetween('created_at', $this->range());
    }

    private function range(): array
    {
        return [$this->from . ' 00:00:00', $this->to . ' 23:59:59'];
    }

    private function monthsInRange(): int
    {
        $a = new \DateTime(substr($this->from, 0, 7) . '-01');
        $b = new \DateTime(substr($this->to, 0, 7) . '-01');
        return (int) $a->diff($b)->m + ((int) $a->diff($b)->y * 12) + 1;
    }

    private function pc($n, $d): string
    {
        return $d ? round($n * 100 / $d, 1) . '%' : '—';
    }

    private function h(string $t): void
    {
        $this->line('');
        $this->line('<options=bold>' . $t . '</>');
        $this->line(str_repeat('-', 64));
    }

    private function kv(string $k, $v): void
    {
        $this->line("  {$k}: <options=bold>{$v}</>");
    }

    private function note(string $t): void
    {
        $this->line("  <comment>> {$t}</comment>");
    }

    private function warnLine(string $t): void
    {
        $this->line("  <fg=yellow>! {$t}</>");
    }

    private function gap(string $what, string $why): void
    {
        $this->gaps[$what] = $why;
    }

    private function gapReport(): void
    {
        if (! $this->gaps) return;
        $this->line('');
        $this->error('غير متاح — بسببه، لا بتقدير:');
        foreach ($this->gaps as $what => $why) {
            $this->line("  * <options=bold>{$what}</> — {$why}");
        }
    }
}
