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
        Schema::create('certificate_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained('certificate')->cascadeOnDelete();
            $table->date('old_expiry_date');
            $table->date('new_expiry_date');
            $table->foreignId('renewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('renewed_by_email')->nullable();
            $table->enum('renewal_method', ['email_link', 'admin_manual', 'admin_bulk']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_renewals');
    }
};
