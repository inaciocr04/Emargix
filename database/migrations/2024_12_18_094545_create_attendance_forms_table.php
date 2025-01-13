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
            $table->foreignId('teacher_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('form_unique_code')->nullable();
            $table->text('signature_teacher')->nullable();
            $table->dateTime('event_date')->nullable();
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
