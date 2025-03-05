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
            $table->enum('loading_photos', ['yes', 'no', 'n/a'])->nullable();
            $table->enum('unloading_photos', ['yes', 'no', 'n/a'])->nullable();
            $table->enum('waybill_signed_at_loading', ['yes', 'no', 'n/a'])->nullable();
            $table->enum('waybill_signed_at_unloading', ['yes', 'no', 'n/a'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn([
                'loading_photos',
                'unloading_photos',
                'waybill_signed_at_loading',
                'waybill_signed_at_unloading'
            ]);
        });
    }
};
