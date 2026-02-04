<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTriggerFieldsToMailTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->string('trigger_type')->nullable()->after('subject');
            $table->boolean('is_active')->default(true)->after('trigger_type');
            $table->boolean('is_automatic')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->dropColumn(['trigger_type', 'is_active', 'is_automatic']);
        });
    }
}
