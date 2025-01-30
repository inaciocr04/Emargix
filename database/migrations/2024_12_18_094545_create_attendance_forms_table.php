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
        Schema::create('attendance_forms', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id');
            $table->string('event_name');
            $table->string('event_end_hour');
            $table->string('event_start_hour');
            $table->foreignId('teacher_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('signature_teacher')->nullable();
            $table->string('event_date')->nullable();
            $table->string('token')->nullable();
            $table->foreignId('training_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->foreignId('td_group_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('tp_group_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_forms');
    }
};
