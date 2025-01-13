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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('lastname');
            $table->string('firstname');
            $table->string('email')->unique();
            $table->string('event_training')->nullable();
            $table->foreignId('training_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('tp_group_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('td_group_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
