<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_checklist_items', function (Blueprint $table) {
            $table->id();
            // Company (stap 1-3) or AcademyTraining (stap 4-6)
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedTinyInteger('step');
            $table->string('item_key', 80);
            // Recurring items (monthly overview): e.g. 2026-11
            $table->string('period', 20)->default('');
            $table->date('due_date')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->unsignedBigInteger('done_by_user_id')->nullable();
            $table->boolean('not_applicable')->default(false);
            $table->string('not_applicable_reason')->nullable();
            $table->unsignedBigInteger('mailing_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['subject_type', 'subject_id', 'item_key', 'period'], 'academy_items_unique');
            $table->index(['subject_type', 'subject_id', 'step']);
            $table->index(['due_date']);
            $table->foreign('done_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('mailing_id')->references('id')->on('mailings')->nullOnDelete();
        });

        Schema::create('academy_routine_checks', function (Blueprint $table) {
            $table->id();
            $table->date('check_date');
            $table->string('check_key', 80);
            $table->unsignedBigInteger('done_by_user_id')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->timestamps();

            $table->unique(['check_date', 'check_key']);
            $table->foreign('done_by_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_routine_checks');
        Schema::dropIfExists('academy_checklist_items');
    }
};
