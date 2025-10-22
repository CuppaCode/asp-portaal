<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('start_fee', 10, 2)->nullable();
            $table->decimal('claims_fee', 10, 2)->nullable();
            $table->decimal('additional_costs', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['start_fee', 'claims_fee', 'additional_costs']);
        });
    }
};
