<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_claim_form_configs', function (Blueprint $table) {
            $table->string('field_width')->default('full')->after('display_order');
            $table->string('field_group')->nullable()->after('field_width');
        });
    }

    public function down(): void
    {
        Schema::table('company_claim_form_configs', function (Blueprint $table) {
            $table->dropColumn(['field_width', 'field_group']);
        });
    }
};
