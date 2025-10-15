<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('bank_account_number')->nullable();
            $table->string('company_size')->nullable();
            $table->integer('truck_count')->nullable();
            $table->text('additional_information')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'bank_account_number',
                'company_size',
                'truck_count',
                'additional_information',
            ]);
        });
    }
};
