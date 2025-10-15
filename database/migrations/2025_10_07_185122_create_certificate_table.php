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
        Schema::create('certificate', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
            $table->timestamps();
            $table->string('name');
            $table->date('notify_date');
            $table->date('expiry_date');
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('cascade');
        });

        Schema::table('mail_templates', function (Blueprint $table) {
            $table->smallInteger('is_certificate_template')->nullable()->after('subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate');

        Schema::table('mail_templates', function (Blueprint $table) {
            $table->dropColumn('is_certificate_template');
        });
    }
};
