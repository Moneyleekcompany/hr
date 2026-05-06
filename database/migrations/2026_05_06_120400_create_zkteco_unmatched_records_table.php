<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('zkteco_unmatched_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->nullable()->constrained('zkteco_devices')->nullOnDelete();
            $table->string('employee_code', 64);
            $table->timestamp('punched_at');
            $table->json('raw_payload')->nullable();
            $table->foreignId('resolved_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->unique(['device_id', 'employee_code', 'punched_at'], 'zkteco_unmatched_unique');
            $table->index(['employee_code', 'punched_at'], 'zkteco_unmatched_emp_at_idx');
            $table->index('resolved_at', 'zkteco_unmatched_resolved_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('zkteco_unmatched_records');
    }
};
