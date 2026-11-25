<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_categories', function (Blueprint $table) {
            if (Schema::hasColumn('certificate_categories', 'duration')) {
                $table->dropColumn('duration');
            }
        });
    }

    public function down(): void
    {
        Schema::table('certificate_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('certificate_categories', 'duration')) {
                $table->integer('duration')->default(12)->after('code');
            }
        });
    }
};