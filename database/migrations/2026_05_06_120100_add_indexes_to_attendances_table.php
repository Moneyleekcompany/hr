<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $existing = $this->existingIndexes();

        Schema::table('attendances', function (Blueprint $table) use ($existing) {
            if (!in_array('attendances_user_date_idx', $existing, true)) {
                $table->index(['user_id', 'attendance_date'], 'attendances_user_date_idx');
            }
            if (!in_array('attendances_company_date_idx', $existing, true)) {
                $table->index(['company_id', 'attendance_date'], 'attendances_company_date_idx');
            }
            if (!in_array('attendances_date_idx', $existing, true)) {
                $table->index('attendance_date', 'attendances_date_idx');
            }
            if (!in_array('attendances_office_time_id_idx', $existing, true)
                && Schema::hasColumn('attendances', 'office_time_id')) {
                $table->index('office_time_id', 'attendances_office_time_id_idx');
            }
        });
    }

    public function down()
    {
        $existing = $this->existingIndexes();

        Schema::table('attendances', function (Blueprint $table) use ($existing) {
            foreach ([
                'attendances_user_date_idx',
                'attendances_company_date_idx',
                'attendances_date_idx',
                'attendances_office_time_id_idx',
            ] as $name) {
                if (in_array($name, $existing, true)) {
                    $table->dropIndex($name);
                }
            }
        });
    }

    /**
     * @return array<string>
     */
    private function existingIndexes(): array
    {
        $driver = DB::connection()->getDriverName();
        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            // Schema introspection يختلف بين السائقين، نعتمد على الفحص الافتراضي
            return [];
        }

        $database = DB::connection()->getDatabaseName();
        $rows = DB::select(
            "SELECT INDEX_NAME FROM information_schema.statistics WHERE table_schema = ? AND table_name = ?",
            [$database, 'attendances']
        );

        return array_values(array_unique(array_map(fn ($r) => $r->INDEX_NAME, $rows)));
    }
};
