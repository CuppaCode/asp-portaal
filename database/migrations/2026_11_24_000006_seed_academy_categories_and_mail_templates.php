<?php

use Database\Seeders\AcademyCertificateCategorySeeder;
use Database\Seeders\AcademyMailTemplateSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Existing installations do not re-run the seeders: add the Academy document types
     * and mail templates here (both seeders are idempotent).
     */
    public function up(): void
    {
        if (Schema::hasTable('certificate_categories')) {
            app(AcademyCertificateCategorySeeder::class)->run();
        }

        if (Schema::hasTable('mail_templates')) {
            app(AcademyMailTemplateSeeder::class)->run();
        }
    }

    public function down(): void
    {
        // The Academy document types and templates stay; they may be in use.
    }
};
