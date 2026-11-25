<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_categories', function (Blueprint $table) {
            $table->string('code')->nullable()->after('name');
        });

        $categories = DB::table('certificate_categories')->select('id', 'name')->get();

        foreach ($categories as $category) {
            $base = Str::slug((string) $category->name, '_');
            $base = $base !== '' ? $base : 'category';
            $code = $base;
            $counter = 1;

            while (DB::table('certificate_categories')->where('code', $code)->where('id', '!=', $category->id)->exists()) {
                $code = $base . '_' . $counter;
                $counter++;
            }

            DB::table('certificate_categories')
                ->where('id', $category->id)
                ->update(['code' => $code]);
        }

        Schema::table('certificate_categories', function (Blueprint $table) {
            $table->unique('code');
        });
    }

    public function down(): void
    {
        Schema::table('certificate_categories', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};