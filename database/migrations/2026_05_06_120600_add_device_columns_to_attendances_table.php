<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'check_in_device_id')) {
                $table->string('check_in_device_id', 128)->nullable()->after('check_out_image');
            }
            if (!Schema::hasColumn('attendances', 'check_out_device_id')) {
                $table->string('check_out_device_id', 128)->nullable()->after('check_in_device_id');
            }
            if (!Schema::hasColumn('attendances', 'check_in_accuracy_m')) {
                $table->unsignedInteger('check_in_accuracy_m')->nullable()->after('check_out_device_id');
            }
            if (!Schema::hasColumn('attendances', 'check_out_accuracy_m')) {
                $table->unsignedInteger('check_out_accuracy_m')->nullable()->after('check_in_accuracy_m');
            }
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            foreach ([
                'check_out_accuracy_m',
                'check_in_accuracy_m',
                'check_out_device_id',
                'check_in_device_id',
            ] as $col) {
                if (Schema::hasColumn('attendances', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
