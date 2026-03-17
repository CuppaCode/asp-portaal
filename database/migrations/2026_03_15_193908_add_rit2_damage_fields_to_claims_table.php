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
            $table->text('damaged_part_2')->nullable()->after('damaged_area');
            $table->text('damage_origin_2')->nullable()->after('damaged_part_2');
            $table->text('damaged_area_2')->nullable()->after('damage_origin_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn(['damaged_part_2', 'damage_origin_2', 'damaged_area_2']);
        });
    }
};
