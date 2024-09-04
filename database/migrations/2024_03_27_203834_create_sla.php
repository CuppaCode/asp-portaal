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
        Schema::create('SLA', function (Blueprint $table) {
            $table->id();
            $table->date('startdate')->nullable();
            $table->date('enddate')->nullable();
            $table->float('amount_users')->nullable();
            $table->string('label')->nullable();
            $table->float('max_amount')->nullable();
            $table->string('reports')->nullable();
            $table->string('analytics_options')->nullable();
            $table->foreignId('company_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SLA');
    }
};
