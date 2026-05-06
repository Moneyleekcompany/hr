<?php

use Illuminate\Database\Migrations\Migration;

// تكرار من 2026_04_29_153748 — تُرك no-op عمدًا.
// الإضافة الفعلية للأعمدة تتم في الميجريشن الأقدم وفي
// 2026_05_06_120000_ensure_attendance_image_columns.php للقواعد الإنتاجية.
return new class extends Migration
{
    public function up()
    {
        // intentionally empty
    }

    public function down()
    {
        // intentionally empty
    }
};
