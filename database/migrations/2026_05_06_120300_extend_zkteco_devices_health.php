<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('zkteco_devices', function (Blueprint $table) {
            if (!Schema::hasColumn('zkteco_devices', 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('zkteco_devices', 'last_punch_at')) {
                $table->timestamp('last_punch_at')->nullable()->after('last_synced_at');
            }
            if (!Schema::hasColumn('zkteco_devices', 'last_run_status')) {
                $table->string('last_run_status', 16)->nullable()->after('last_punch_at');
            }
            if (!Schema::hasColumn('zkteco_devices', 'last_error_message')) {
                $table->text('last_error_message')->nullable()->after('last_run_status');
            }
            if (!Schema::hasColumn('zkteco_devices', 'consecutive_failures')) {
                $table->unsignedInteger('consecutive_failures')->default(0)->after('last_error_message');
            }
        });
    }

    public function down()
    {
        Schema::table('zkteco_devices', function (Blueprint $table) {
            foreach ([
                'consecutive_failures',
                'last_error_message',
                'last_run_status',
                'last_punch_at',
                'last_synced_at',
            ] as $col) {
                if (Schema::hasColumn('zkteco_devices', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
