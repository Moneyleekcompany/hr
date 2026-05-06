<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // No-op: تكرار من 2026_04_28_164600_create_zkteco_devices_table.
        // تُرك للحفاظ على سجل migrations في البيئات التي شغّلته.
        // لا تحذف الملف لأن حذفه يكسر `php artisan migrate:status` على
        // قواعد البيانات التي سبق وسجّلت هذا الميجريشن.
    }

    public function down(): void
    {
        // intentionally empty
    }
};
