<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('name');
            $table->string('cbr_code', 50)->nullable();
            $table->date('training_date');
            $table->string('location')->nullable();
            // asp = eigen training, partner = externe opleider
            $table->string('provider_type', 20)->default('asp');
            $table->string('provider_name')->nullable();
            $table->string('provider_email')->nullable();
            // Empty hours = no Code 95 module.
            $table->unsignedSmallInteger('code95_hours')->nullable();
            $table->string('code95_type', 20)->nullable();
            $table->boolean('is_prevention')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'training_date']);
        });

        Schema::create('academy_training_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('academy_trainings')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->boolean('attended')->nullable();
            $table->boolean('cbr_confirmed')->default(false);
            $table->unsignedBigInteger('code95_course_id')->nullable();
            $table->timestamps();

            $table->unique(['training_id', 'driver_id']);
            $table->foreign('code95_course_id')->references('id')->on('code95_courses')->nullOnDelete();
        });

        Schema::table('code95_courses', function (Blueprint $table) {
            // Only hours confirmed in the CBR register count towards the 35 hours.
            $table->boolean('cbr_registered')->default(true)->after('hours');
            $table->date('valid_until')->nullable()->after('cbr_registered');
            $table->unsignedBigInteger('academy_training_id')->nullable()->after('valid_until');

            $table->foreign('academy_training_id')->references('id')->on('academy_trainings')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('code95_courses', function (Blueprint $table) {
            $table->dropForeign(['academy_training_id']);
            $table->dropColumn(['cbr_registered', 'valid_until', 'academy_training_id']);
        });

        Schema::dropIfExists('academy_training_participants');
        Schema::dropIfExists('academy_trainings');
    }
};
