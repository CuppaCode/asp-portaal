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
        if (!Schema::hasColumn('claims', 'insurance_company_id')) {
            Schema::table('claims', function (Blueprint $table) {
                $table->unsignedBigInteger('insurance_company_id')->nullable()->after('id');
                $table->foreign('insurance_company_id')->references('id')->on('companies')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign(['insurance_company_id']);
            $table->dropColumn('insurance_company_id');
        });
    }
};
