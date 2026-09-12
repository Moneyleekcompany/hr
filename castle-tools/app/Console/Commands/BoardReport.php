<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * حزمة قرارات المجلس — الأرقام التشغيلية آليًا من القاعدة.
 *
 * مبني على منهجية تقرير «كاسيل — ما بعد البيع · يناير–أغسطس ٢٠٢٦»:
 * كل رقم له قاعدة معلنة، ولا يُقارن رقم بغير قاعدته.
 *
 * قراءة فقط — لا يكتب في القاعدة ولا يعدّل صفًا واحدًا.
 *
 *   php artisan board:report --probe              فحص الأعمدة الموجودة فقط
 *   php artisan board:report --from=2026-01-01 --to=2026-08-31
 *   php artisan board:report --from=... --to=... --json
 *
 * ما لا يمكن حسابه يُطبع تحت «غير متاح» بسببه — لا يُخمَّن ولا يُملأ بصفر.
 */
class BoardReport extends Command
{
    protected $signature = 'board:report
        {--from= : بداية الفترة YYYY-MM-DD}
        {--to= : نهاية الفترة YYYY-MM-DD}
        {--probe : يطبع أعمدة الجداول الحاكمة ويخرج}
        {--json : مخرَج JSON بدل الجداول}';

    protected $description = 'أرقام لوحة المجلس من قاعدة البيانات — قراءة فقط';

    /** الجداول الحاكمة التي يقوم عليها التقرير */
    private const CORE_TABLES = [
        'tickets', 'ticket_statuses', 'ticket_statuses_histories', 'ticket_reschedules',
        'ticket_surveys', 'ticket_notes', 'ticket_parts', 'customers', 'addresses',
        'users', 'units', 'branches', 'areas', 'cities', 'sub_categories',
        'errors', 'error_links', 'collections', 'transactions', 'expenses',
        'hr_payroll_runs', 'hr_payroll_lines', 'sub_department_rates', 'pay_settings',
        'stock_orders', 'stock_order_details', 'spare_parts', 'part_cost_movements',
    ];

    private array $out = [];
    private array $missing = [];
    private string $from;
    private string $to;

    public function handle(): int
    {
        if ($this->option('probe')) {
            return $this->probe();
        }

        $this->from = $this->option('from') ?: now()->startOfYear()->toDateString();
        $this->to   = $this->option('to')   ?: now()->toDateString();

        $this->line('');
        $this->info("حزمة قرارات المجلس · {$this->from} → {$this->to}");
        $this->line(str_repeat('═', 62));

        $this->sectionVolumes();
        $this->sectionActivitySplit();
        $this->sectionCancellations();
        $this->sectionReschedules();
        $this->sectionSatisfaction();
        $this->sectionNetwork();
        $this->sectionGeography();
        $this->sectionCost();

        $this->reportMissing();

        if ($this->option('json')) {
            $this->line(json_encode(
                ['period' => [$this->from, $this->to], 'metrics' => $this->out, 'unavailable' => $this->missing],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ));
        }

        return self::SUCCESS;
    }

    // ── فحص الأعمدة ────────────────────────────────────────────────
    private function probe(): int
    {
        foreach (self::CORE_TABLES as $t) {
            if (! Schema::hasTable($t)) {
                $this->line("❌ {$t} — الجدول غير موجود");
                continue;
            }
            $cols = Schema::getColumnListing($t);
            $n    = DB::table($t)->count();
            $this->line('');
            $this->info("✔ {$t}  ({$n} صف)");
            $this->line('   ' . implode(' · ', $cols));
        }
        return self::SUCCESS;
    }

    // ── ١) الأحجام ─────────────────────────────────────────────────
    private function sectionVolumes(): void
    {
        if (! $this->need('tickets', ['created_at'])) return;

        $base = $this->tickets();
        $total = (clone $base)->count();
        $this->put('إجمالي التذاكر', $total, 'كل الحالات في الفترة');

        // الحالة تُقرأ من ticket_statuses لا من قيمة مكتوبة بيد
        $statusCol = $this->firstCol('tickets', ['status_id', 'ticket_status_id', 'status']);
        if (! $statusCol) {
            $this->miss('المنجَز/الملغى/المفتوح', 'لم أجد عمود الحالة في tickets');
            return;
        }

        $rows = (clone $base)
            ->select($statusCol, DB::raw('COUNT(*) as n'))
            ->groupBy($statusCol)->orderByDesc('n')->get();

        $names = Schema::hasTable('ticket_statuses')
            ? DB::table('ticket_statuses')->pluck(
                $this->firstCol('ticket_statuses', ['name_ar', 'name', 'title']) ?: 'id', 'id'
            )->all()
            : [];

        $this->line('');
        $this->info('توزيع الحالات');
        $this->table(['الحالة', 'العدد', '%'], $rows->map(fn ($r) => [
            $names[$r->$statusCol] ?? (string) $r->$statusCol,
            number_format($r->n),
            $total ? round($r->n * 100 / $total, 1) . '%' : '—',
        ])->all());

        $this->note('صفحة ٣ في الحزمة: مجموع الحالات لازم يساوي إجمالي التذاكر بالضبط.');
    }

    // ── ٢) فصل بلاغات العملاء عن مرتجعات الورشة ────────────────────
    private function sectionActivitySplit(): void
    {
        $this->note('فصل «بلاغات عملاء» عن «مرتجعات ورشة» يحتاج تعريف حسابات المصنع '
            . 'والمرتجعات — وهو تعريف إداري لا عمود واحد. راجع §٤ في ملف التسليم.');
        $this->miss('نسبة الإنجاز المعتمدة (78.8%)',
            'تحتاج استبعاد حسابات المصنع والمرتجعات والتجار والتركيب — قواعد إدارية تُحدَّد أولًا');
    }

    // ── ٣) الإلغاء والتسجيل المزدوج ────────────────────────────────
    private function sectionCancellations(): void
    {
        $serial = $this->firstCol('tickets', ['serial', 'serial_number', 'device_serial', 'unit_serial']);
        if (! $serial) {
            $this->miss('التسجيل المزدوج (2,606)', 'لم أجد عمود الرقم التسلسلي في tickets');
            return;
        }
        $this->note("عمود السيريال المستعمل: tickets.{$serial} — أكّده قبل الاعتماد.");
    }

    // ── ٤) إعادة الجدولة ───────────────────────────────────────────
    private function sectionReschedules(): void
    {
        if (! Schema::hasTable('ticket_reschedules')) {
            $this->miss('إعادة الجدولة', 'جدول ticket_reschedules غير موجود');
            return;
        }
        $all    = DB::table('ticket_reschedules')->count();
        $period = $this->inPeriod(DB::table('ticket_reschedules'), 'ticket_reschedules');
        $n      = $period ? $period->count() : null;

        $this->line('');
        $this->info('إعادة الجدولة');
        $this->table(['البند', 'القيمة'], array_filter([
            ['عمليات الجدولة — كل التاريخ', number_format($all)],
            $n !== null ? ['عمليات الجدولة — في الفترة', number_format($n)] : null,
        ]));

        $this->note('⚠️ حزمة المجلس تذكر قاعدة 35,507 عملية جدولة. لو الرقم هنا أقل بكثير '
            . 'فالمصدر مختلف (لوحة التحكم التحليلية؟) — يُحسم قبل بناء مؤشر آلي عليه.');
    }

    // ── ٥) الرضا ───────────────────────────────────────────────────
    private function sectionSatisfaction(): void
    {
        if (! Schema::hasTable('ticket_surveys')) {
            $this->miss('الرضا المُبلَّغ', 'جدول ticket_surveys غير موجود');
            return;
        }
        $q = $this->inPeriod(DB::table('ticket_surveys'), 'ticket_surveys');
        if (! $q) { $this->miss('الرضا', 'لا عمود تاريخ في ticket_surveys'); return; }

        $n = $q->count();
        $scoreCol = $this->firstCol('ticket_surveys', ['score', 'rate', 'rating', 'value', 'result']);

        $this->line('');
        $this->info('الرضا المُبلَّغ');
        $rows = [['عدد التقييمات في الفترة', number_format($n)]];

        if ($scoreCol) {
            $dist = (clone $q)->select($scoreCol, DB::raw('COUNT(*) as c'))
                ->groupBy($scoreCol)->orderBy($scoreCol)->get();
            foreach ($dist as $d) {
                $rows[] = ["  درجة {$d->$scoreCol}", number_format($d->c)
                    . ($n ? ' (' . round($d->c * 100 / $n, 1) . '%)' : '')];
            }
        } else {
            $this->miss('توزيع التقييمات', 'لم أجد عمود الدرجة في ticket_surveys');
        }
        $this->table(['البند', 'القيمة'], $rows);
        $this->note('التغطية تُعرض دائمًا مع الرقم — 97.2 بلا تغطية 25.2% رقم ناقص (صفحة ١١).');
    }

    // ── ٦) الشبكة ──────────────────────────────────────────────────
    private function sectionNetwork(): void
    {
        $centerCol = $this->firstCol('tickets', ['center_id', 'unit_id']);
        if (! $centerCol) { $this->miss('تركّز الشبكة (47.9%)', 'لم أجد عمود المركز في tickets'); return; }

        $base  = $this->tickets();
        $total = (clone $base)->whereNotNull($centerCol)->count();
        if (! $total) { $this->miss('تركّز الشبكة', 'لا بلاغات مسنَدة لمركز في الفترة'); return; }

        $top = (clone $base)->whereNotNull($centerCol)
            ->select($centerCol, DB::raw('COUNT(*) as n'))
            ->groupBy($centerCol)->orderByDesc('n')->limit(10)->get();

        $this->line('');
        $this->info('أعلى عشرة مراكز حملًا');
        $this->table(['المركز', 'بلاغات', '% من المسنَد'], $top->map(fn ($r) => [
            (string) $r->$centerCol, number_format($r->n), round($r->n * 100 / $total, 1) . '%',
        ])->all());

        $share = round($top->sum('n') * 100 / $total, 1);
        $this->put('تركّز أعلى ١٠ مراكز', $share . '%', "من {$total} بلاغ مسنَد");
        $this->note("الحزمة تذكر 47.9% ومستهدف ٤٠٪ — المقاس هنا {$share}%.");
    }

    // ── ٧) الجغرافيا ───────────────────────────────────────────────
    private function sectionGeography(): void
    {
        $cityCol = $this->firstCol('tickets', ['city_id', 'governorate_id', 'area_id']);
        if (! $cityCol) { $this->miss('خريطة الطلب', 'لم أجد عمود الجغرافيا في tickets'); return; }
        $this->note("الجغرافيا متاحة عبر tickets.{$cityCol} — الخريطة تُبنى بعد تأكيد العمود.");
    }

    // ── ٨) التكلفة ─────────────────────────────────────────────────
    private function sectionCost(): void
    {
        $this->line('');
        $this->info('تكلفة التشغيل — المصادر');

        $rows = [];

        // المرتبات والحوافز
        if (Schema::hasTable('hr_payroll_lines') && Schema::hasTable('hr_payroll_runs')) {
            $runs  = DB::table('hr_payroll_runs')->count();
            $lines = DB::table('hr_payroll_lines')->count();
            $rows[] = ['المرتبات والحوافز', "{$runs} مسيّر · {$lines} سطر"];
            if ($runs < 8) {
                $this->miss('المرتبات ليناير–أغسطس',
                    "المحرك موجود لكن {$runs} مسيّر فقط مقيَّد — الشهور الباقية بلا مسيّر في النظام");
            }
        } else {
            $this->miss('المرتبات', 'جداول hr_payroll_* غير موجودة');
        }

        // المصروفات التشغيلية
        if (Schema::hasTable('expenses')) {
            $n = DB::table('expenses')->count();
            $amountCol = $this->firstCol('expenses', ['amount', 'value', 'total', 'price']);
            $q = $this->inPeriod(DB::table('expenses'), 'expenses');
            $sum = ($amountCol && $q) ? (float) (clone $q)->sum($amountCol) : null;
            $rows[] = ['المصروفات التشغيلية', $n . ' صف'
                . ($sum !== null ? ' · ' . number_format($sum, 2) . ' ج في الفترة' : '')];
            $this->miss('المصروفات التشغيلية (1,370,479 ج)',
                'الشاشة موجودة لكن البنود السبعة (سولار · إيجار فروع · سيستم المكالمات · '
                . 'سكن · شحن قطع · الرقم المختصر · مشاريب) تحتاج تصنيفًا وفترة');
        } else {
            $this->miss('المصروفات', 'جدول expenses غير موجود');
        }

        // الإهلاك
        $this->miss('إهلاك السيارات والعدة (520,000 ج)',
            'لا جدول أصول في القاعدة — الأساس «قيمة الإحلال ÷ العمر المتبقي» لا القيمة الدفترية');

        if ($rows) $this->table(['البند', 'الحالة'], $rows);

        $this->note('تكلفة البلاغ 202.8 ج = (مرتبات + مصاريف + حوافز مراكز + إهلاك) ÷ المنجَز. '
            . 'أي بند ناقص يجعل الرقم غير قابل للإصدار — لا يُقدَّر.');
    }

    // ── أدوات ──────────────────────────────────────────────────────
    private function tickets()
    {
        $q = DB::table('tickets')->whereBetween('created_at', [$this->from . ' 00:00:00', $this->to . ' 23:59:59']);
        if (Schema::hasColumn('tickets', 'deleted_at')) $q->whereNull('deleted_at');
        return $q;
    }

    private function inPeriod($q, string $table)
    {
        $col = $this->firstCol($table, ['created_at', 'date', 'expense_date', 'submitted_at']);
        if (! $col) return null;
        $q = $q->whereBetween($col, [$this->from . ' 00:00:00', $this->to . ' 23:59:59']);
        if (Schema::hasColumn($table, 'deleted_at')) $q->whereNull('deleted_at');
        return $q;
    }

    private function firstCol(string $table, array $candidates): ?string
    {
        if (! Schema::hasTable($table)) return null;
        foreach ($candidates as $c) if (Schema::hasColumn($table, $c)) return $c;
        return null;
    }

    private function need(string $table, array $cols): bool
    {
        if (! Schema::hasTable($table)) { $this->miss($table, 'الجدول غير موجود'); return false; }
        foreach ($cols as $c) {
            if (! Schema::hasColumn($table, $c)) { $this->miss($table, "العمود {$c} غير موجود"); return false; }
        }
        return true;
    }

    private function put(string $k, $v, string $basis = ''): void
    {
        $this->out[$k] = ['value' => $v, 'basis' => $basis];
    }

    private function miss(string $what, string $why): void
    {
        $this->missing[$what] = $why;
    }

    private function note(string $t): void
    {
        $this->line("  <comment>▸ {$t}</comment>");
    }

    private function reportMissing(): void
    {
        if (! $this->missing) return;
        $this->line('');
        $this->error('غير متاح اليوم — بسببه، لا بتقدير:');
        foreach ($this->missing as $what => $why) {
            $this->line("  • <options=bold>{$what}</> — {$why}");
        }
    }
}
