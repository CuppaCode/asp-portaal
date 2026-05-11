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
        Schema::table('claims', function (Blueprint $table) {
            $table->text('denied_reason')->nullable()->after('status');
            $table->timestamp('draft_expires_at')->nullable()->after('denied_reason');
            $table->timestamp('last_reminder_sent_at')->nullable()->after('draft_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn(['denied_reason', 'draft_expires_at', 'last_reminder_sent_at']);
        });
    }
};
