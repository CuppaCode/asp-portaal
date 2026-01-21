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
        Schema::table('companies', function (Blueprint $table) {
            $table->integer('draft_expiry_days')->default(30)->after('additional_information');
            $table->integer('draft_reminder_days')->default(7)->after('draft_expiry_days');
            $table->integer('draft_reminder_frequency_days')->default(7)->after('draft_reminder_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['draft_expiry_days', 'draft_reminder_days', 'draft_reminder_frequency_days']);
        });
    }
};
