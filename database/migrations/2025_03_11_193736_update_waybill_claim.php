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
            $table->enum('loading_photos', ['yes', 'no', 'n/a'])->nullable()->change();
            $table->enum('unloading_photos', ['yes', 'no', 'n/a'])->nullable()->change();
            $table->enum('waybill_signed_at_loading', ['yes', 'no', 'n/a'])->nullable()->change();
            $table->enum('waybill_signed_at_unloading', ['yes', 'no', 'n/a'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->boolean('loading_photos')->default(0)->nullable()->change();
            $table->boolean('unloading_photos')->default(0)->nullable()->change();
            $table->boolean('waybill_signed_at_loading')->default(0)->nullable()->change();
            $table->boolean('waybill_signed_at_unloading')->default(0)->nullable()->change();
        });
    }
};
