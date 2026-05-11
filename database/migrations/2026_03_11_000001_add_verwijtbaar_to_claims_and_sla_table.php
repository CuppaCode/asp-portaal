<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            if (!Schema::hasColumn('claims', 'verwijtbaar')) {
                $table->enum('verwijtbaar', ['yes', 'no'])->nullable()->after('damage_kind');
            }
            if (!Schema::hasColumn('claims', 'verwijtbaar_mail_sent_at')) {
                $table->timestamp('verwijtbaar_mail_sent_at')->nullable()->after('verwijtbaar');
            }
        });

        Schema::table('SLA', function (Blueprint $table) {
            if (!Schema::hasColumn('SLA', 'verwijtbaar_mail_enabled')) {
                $table->boolean('verwijtbaar_mail_enabled')->default(false)->after('other');
            }
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn(['verwijtbaar', 'verwijtbaar_mail_sent_at']);
        });

        Schema::table('SLA', function (Blueprint $table) {
            $table->dropColumn('verwijtbaar_mail_enabled');
        });
    }
};
