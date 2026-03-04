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
        Schema::table('certificate_categories', function (Blueprint $table) {
            $table->integer('notify_days_before')->default(30)->after('duration');
            $table->boolean('enable_notifications')->default(true)->after('notify_days_before');
            $table->json('notification_recipients')->nullable()->after('enable_notifications');
            $table->integer('reminder_frequency_days')->default(7)->after('notification_recipients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_categories', function (Blueprint $table) {
            $table->dropColumn(['notify_days_before', 'enable_notifications', 'notification_recipients', 'reminder_frequency_days']);
        });
    }
};
