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
        Schema::create('employee_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('office_time_id')->nullable()->constrained('office_times')->nullOnDelete();
            $table->date('date');
            $table->boolean('is_off_day')->default(false); // تحديد إذا كان هذا اليوم إجازة استثنائية
            $table->text('remark')->nullable();
            $table->timestamps();

            // منع تكرار ورديتين لنفس الموظف في نفس اليوم
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_rosters');
    }
};