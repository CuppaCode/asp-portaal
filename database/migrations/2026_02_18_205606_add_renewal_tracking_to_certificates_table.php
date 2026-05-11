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
        Schema::table('certificate', function (Blueprint $table) {
            $table->timestamp('last_notification_sent_at')->nullable()->after('expiry_date');
            $table->string('renewal_token')->nullable()->unique()->after('last_notification_sent_at');
            $table->timestamp('renewal_token_expires_at')->nullable()->after('renewal_token');
            $table->date('original_expiry_date')->nullable()->after('renewal_token_expires_at');
            $table->string('renewed_by_email')->nullable()->after('original_expiry_date');
            $table->foreignId('renewed_by_user_id')->nullable()->constrained('users')->nullOnDelete()->after('renewed_by_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate', function (Blueprint $table) {
            $table->dropForeign(['renewed_by_user_id']);
            $table->dropColumn(['last_notification_sent_at', 'renewal_token', 'renewal_token_expires_at', 'original_expiry_date', 'renewed_by_email', 'renewed_by_user_id']);
        });
    }
};
