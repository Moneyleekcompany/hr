<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_edits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendances')->cascadeOnDelete();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 16); // created | updated | deleted
            $table->string('field', 64)->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('reason', 500)->nullable();
            $table->string('ip_address', 64)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['attendance_id', 'created_at'], 'attendance_edits_att_created_idx');
            $table->index('edited_by', 'attendance_edits_edited_by_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_edits');
    }
};
