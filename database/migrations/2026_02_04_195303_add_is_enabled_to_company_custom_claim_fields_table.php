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
        Schema::table('company_custom_claim_fields', function (Blueprint $table) {
            $table->boolean('is_enabled')->default(true)->after('field_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_custom_claim_fields', function (Blueprint $table) {
            $table->dropColumn('is_enabled');
        });
    }
};
