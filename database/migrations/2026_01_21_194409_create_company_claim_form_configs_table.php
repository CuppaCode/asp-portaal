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
        Schema::create('company_claim_form_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('field_name');
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_required')->default(false);
            $table->boolean('include_in_notification')->default(false);
            $table->string('notification_label')->nullable();
            $table->json('conditional_logic')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade');

            $table->unique(['company_id', 'field_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_claim_form_configs');
    }
};
