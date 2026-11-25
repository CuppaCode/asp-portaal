<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('code95_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained('certificate')->cascadeOnDelete();
            $table->date('course_date');
            $table->string('name');
            $table->string('code', 50);
            $table->enum('type', ['theory', 'practice']);
            $table->unsignedInteger('hours');
            $table->string('trainer')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();

            $table->index(['certificate_id', 'course_date']);
            $table->index(['team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('code95_courses');
    }
};
