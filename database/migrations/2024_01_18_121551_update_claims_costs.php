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
            $table->decimal('damage_costs', 15, 2)->default(0.00)->nullable()->change();
            $table->decimal('recovery_costs', 15, 2)->default(0.00)->nullable()->change();
            $table->decimal('replacement_vehicle_costs', 15, 2)->default(0.00)->nullable()->change();
            $table->decimal('expert_costs', 15, 2)->default(0.00)->nullable()->change();
            $table->decimal('other_costs', 15, 2)->default(0.00)->nullable()->change();
            $table->decimal('deductible_excess_costs', 15, 2)->default(0.00)->nullable()->change();
            $table->decimal('insurance_costs', 15, 2)->default(0.00)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
