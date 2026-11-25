<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $code95 = DB::table('certificate_categories')
            ->where(function ($query) {
                $query->whereRaw('LOWER(name) = ?', ['code 95'])
                    ->orWhereRaw('LOWER(name) = ?', ['code95'])
                    ->orWhereRaw('LOWER(name) = ?', ['code_95']);
            })
            ->first();

        if (!$code95) {
            $existingCode95 = DB::table('certificate_categories')->where('code', 'code_95')->first();
            if ($existingCode95) {
                return;
            }

            DB::table('certificate_categories')->insert([
                'name' => 'Code 95',
                'code' => 'code_95',
                'notify_days_before' => 30,
                'enable_notifications' => true,
                'reminder_frequency_days' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return;
        }

        $target = DB::table('certificate_categories')->where('code', 'code_95')->first();

        if ($target && $target->id !== $code95->id) {
            DB::table('certificate')->where('category_id', $code95->id)->update(['category_id' => $target->id]);
            DB::table('certificate_categories')->where('id', $code95->id)->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
            return;
        }

        DB::table('certificate_categories')->where('id', $code95->id)->update([
            'code' => 'code_95',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Keep the normalized category code; this migration only normalizes existing data.
    }
};
