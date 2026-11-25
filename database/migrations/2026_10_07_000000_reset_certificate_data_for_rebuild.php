<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rebuild scope explicitly starts from empty certificate entry data.
        DB::table('certificate_renewals')->delete();
        DB::table('certificate')->delete();
    }

    public function down(): void
    {
        // Data reset cannot be reversed.
    }
};