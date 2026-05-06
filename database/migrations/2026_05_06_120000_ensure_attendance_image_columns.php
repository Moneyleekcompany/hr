<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// يضمن وجود أعمدة سيلفي الحضور على بيئات الإنتاج التي شغّلت
// الميجريشنين الفارغين 2026_04_29 / 2026_04_30 قبل إصلاحهما.
return new class extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'check_in_image')) {
                $table->string('check_in_image', 500)->nullable()->after('check_out_longitude');
            }
            if (!Schema::hasColumn('attendances', 'check_out_image')) {
                $table->string('check_out_image', 500)->nullable()->after('check_in_image');
            }
        });
    }

    public function down()
    {
        // لا نتراجع هنا — الأعمدة تُسقَط عبر ميجريشن 2026_04_29.
    }
};
