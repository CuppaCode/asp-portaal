<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // notify_date is calculated from the certificate type; it is no longer entered manually.
        DB::statement('ALTER TABLE certificate MODIFY notify_date DATE NULL');
    }

    public function down(): void
    {
        DB::statement('UPDATE certificate SET notify_date = expiry_date WHERE notify_date IS NULL');
        DB::statement('ALTER TABLE certificate MODIFY notify_date DATE NOT NULL');
    }
};
