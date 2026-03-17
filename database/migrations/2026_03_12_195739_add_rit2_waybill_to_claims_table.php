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
            $table->enum('loading_photos_2', ['yes', 'no', 'n/a'])->nullable()->after('waybill_signed_at_unloading');
            $table->enum('unloading_photos_2', ['yes', 'no', 'n/a'])->nullable()->after('loading_photos_2');
            $table->enum('waybill_signed_at_loading_2', ['yes', 'no', 'n/a'])->nullable()->after('unloading_photos_2');
            $table->enum('waybill_signed_at_unloading_2', ['yes', 'no', 'n/a'])->nullable()->after('waybill_signed_at_loading_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn(['loading_photos_2', 'unloading_photos_2', 'waybill_signed_at_loading_2', 'waybill_signed_at_unloading_2']);
        });
    }
};
