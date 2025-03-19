<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('claims')->update([
            'loading_photos' => null,
            'unloading_photos' => null,
            'waybill_signed_at_loading' => null,
            'waybill_signed_at_unloading' => null
        ]);

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
        DB::table('claims')->update([
            'loading_photos' => DB::raw("
                CASE 
                    WHEN loading_photos = 'yes' THEN 1
                    WHEN loading_photos = 'no' THEN 0
                    ELSE NULL
                END
            "),
            'unloading_photos' => DB::raw("
                CASE 
                    WHEN unloading_photos = 'yes' THEN 1
                    WHEN unloading_photos = 'no' THEN 0
                    ELSE NULL
                END
            "),
            'waybill_signed_at_loading' => DB::raw("
                CASE
                    WHEN waybill_signed_at_loading = 'yes' THEN 1
                    WHEN waybill_signed_at_loading = 'no' THEN 0
                    ELSE NULL
                END
            "),
            'waybill_signed_at_unloading' => DB::raw("
                CASE
                    WHEN waybill_signed_at_unloading = 'yes' THEN 1
                    WHEN waybill_signed_at_unloading = 'no' THEN 0
                    ELSE NULL
                END
            ")
        ]);

        Schema::table('claims', function (Blueprint $table) {
            $table->boolean('loading_photos')->default(0)->nullable()->change();
            $table->boolean('unloading_photos')->default(0)->nullable()->change();
            $table->boolean('waybill_signed_at_loading')->default(0)->nullable()->change();
            $table->boolean('waybill_signed_at_unloading')->default(0)->nullable()->change();
        });
    }
};
