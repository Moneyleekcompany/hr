<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('offline_punch_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('client_uuid');
            $table->timestamp('punched_at');
            $table->string('type', 16); // checkIn | checkOut
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->unsignedInteger('accuracy_meters')->nullable();
            $table->string('device_id', 128)->nullable();
            $table->string('status', 16)->default('pending'); // pending | applied | rejected
            $table->text('error_message')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'client_uuid'], 'offline_punch_user_uuid_unique');
            $table->index(['user_id', 'punched_at'], 'offline_punch_user_at_idx');
            $table->index('status', 'offline_punch_status_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('offline_punch_submissions');
    }
};
