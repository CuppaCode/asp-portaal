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
        Schema::table('vehicle_opposites', function (Blueprint $table) {
            $table->string('chassis_number')->nullable()->after('plates');
            $table->string('build_year')->nullable()->after('chassis_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_opposites', function (Blueprint $table) {
            $table->dropColumn(['chassis_number', 'build_year']);
        });
    }
};
