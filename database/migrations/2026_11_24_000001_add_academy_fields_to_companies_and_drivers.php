<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('academy_enabled')->default(false)->after('active');
            $table->date('academy_started_at')->nullable()->after('academy_enabled');
            $table->date('academy_completed_at')->nullable()->after('academy_started_at');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->boolean('in_service')->default(true)->after('function');
            $table->date('out_of_service_at')->nullable()->after('in_service');
            $table->string('street')->nullable()->after('out_of_service_at');
            $table->string('zipcode', 20)->nullable()->after('street');
            $table->string('city')->nullable()->after('zipcode');
            $table->string('cbr_customer_number', 50)->nullable()->after('city');
            $table->string('driver_pass_number', 50)->nullable()->after('cbr_customer_number');
            $table->boolean('scans_attached')->default(false)->after('driver_pass_number');
            $table->unsignedBigInteger('checked_by_user_id')->nullable()->after('scans_attached');
            $table->timestamp('checked_at')->nullable()->after('checked_by_user_id');

            $table->foreign('checked_by_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['checked_by_user_id']);
            $table->dropColumn([
                'in_service',
                'out_of_service_at',
                'street',
                'zipcode',
                'city',
                'cbr_customer_number',
                'driver_pass_number',
                'scans_attached',
                'checked_by_user_id',
                'checked_at',
            ]);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['academy_enabled', 'academy_started_at', 'academy_completed_at']);
        });
    }
};
