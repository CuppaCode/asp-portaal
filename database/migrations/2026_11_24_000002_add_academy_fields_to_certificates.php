<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Expiry date can be empty: "n.v.t.", one-off documents (berging) or "nog invullen".
        DB::statement('ALTER TABLE certificate MODIFY expiry_date DATE NULL');

        Schema::table('certificate', function (Blueprint $table) {
            $table->boolean('not_applicable')->default(false)->after('expiry_date');
        });

        Schema::table('certificate_categories', function (Blueprint $table) {
            $table->boolean('academy')->default(false)->after('code');
            $table->boolean('one_off')->default(false)->after('academy');
            $table->boolean('allow_not_applicable')->default(true)->after('one_off');
            $table->unsignedTinyInteger('signal_1_months')->nullable()->after('allow_not_applicable');
            $table->unsignedSmallInteger('signal_2_days')->nullable()->after('signal_1_months');
            $table->boolean('signal_1_only_if_hours_open')->default(false)->after('signal_2_days');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('signal_1_only_if_hours_open');
        });

        Schema::create('certificate_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained('certificate')->cascadeOnDelete();
            $table->unsignedTinyInteger('level');
            // A signal belongs to one expiry date: a renewed document starts with new signals.
            $table->date('expiry_date');
            $table->unsignedBigInteger('mailing_id')->nullable();
            $table->unsignedBigInteger('sent_by_user_id')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['certificate_id', 'level', 'expiry_date'], 'certificate_signals_unique');
            $table->foreign('mailing_id')->references('id')->on('mailings')->nullOnDelete();
            $table->foreign('sent_by_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_signals');

        Schema::table('certificate_categories', function (Blueprint $table) {
            $table->dropColumn([
                'academy',
                'one_off',
                'allow_not_applicable',
                'signal_1_months',
                'signal_2_days',
                'signal_1_only_if_hours_open',
                'sort_order',
            ]);
        });

        Schema::table('certificate', function (Blueprint $table) {
            $table->dropColumn('not_applicable');
        });

        DB::statement('UPDATE certificate SET expiry_date = CURDATE() WHERE expiry_date IS NULL');
        DB::statement('ALTER TABLE certificate MODIFY expiry_date DATE NOT NULL');
    }
};
