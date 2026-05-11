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
            $table->unsignedBigInteger('vehicle_2_id')->nullable()->after('vehicle_id');
            $table->foreign('vehicle_2_id')->references('id')->on('vehicles')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign(['vehicle_2_id']);
            $table->dropColumn('vehicle_2_id');
        });
    }
};
