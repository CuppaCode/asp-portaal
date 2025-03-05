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
            $table->boolean('loading_photos')->default(0)->nullable();
            $table->boolean('unloading_photos')->default(0)->nullable();
            $table->boolean('waybill_signed_at_loading')->default(0)->nullable();
            $table->boolean('waybill_signed_at_unloading')->default(0)->nullable();
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
