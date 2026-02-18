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
        Schema::create('company_custom_claim_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('field_type'); // text, textarea, select
            $table->string('field_name'); // unique per company
            $table->string('field_label');
            $table->json('options')->nullable(); // for select fields
            $table->boolean('is_required')->default(false);
            $table->boolean('include_in_notification')->default(false);
            $table->json('conditional_logic')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique(['company_id', 'field_name']);
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->json('custom_fields_data')->nullable()->after('denied_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn('custom_fields_data');
        });
        
        Schema::dropIfExists('company_custom_claim_fields');
    }
};
