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
        Schema::create('kpi_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete(); // المدير الذي قام بالتقييم
            $table->string('month', 2); // مثل: "01", "12"
            $table->year('year'); // مثل: 2024
            $table->decimal('attendance_score', 5, 2)->default(0); // التقييم بناءً على التأخيرات
            $table->decimal('task_score', 5, 2)->default(0); // التقييم بناءً على إنجاز المهام
            $table->decimal('direct_manager_score', 5, 2)->default(0); // تقييم المدير المباشر
            $table->decimal('total_score', 5, 2)->default(0); // المجموع النهائي
            $table->text('feedback')->nullable(); // ملاحظات التطوير
            $table->timestamps();

            // الموظف له تقييم واحد فقط في الشهر والسنة
            $table->unique(['user_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_evaluations');
    }
};