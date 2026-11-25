<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained('certificate')->cascadeOnDelete();
            $table->string('field_key');
            $table->string('value_type')->default('string');
            $table->text('value_text')->nullable();
            $table->decimal('value_number', 15, 4)->nullable();
            $table->date('value_date')->nullable();
            $table->json('value_json')->nullable();
            $table->string('visibility_scope')->default('internal');
            $table->string('masked_display_value')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();

            $table->unique(['certificate_id', 'field_key']);
            $table->index('field_key');
            $table->index(['visibility_scope', 'field_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_meta');
    }
};