<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SetDefaultTriggerTypeForExistingMailTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update all existing mail templates that don't have a trigger_type yet
        // Set them to MANUAL_CLAIMS so they continue to work as before
        DB::table('mail_templates')
            ->whereNull('trigger_type')
            ->update([
                'trigger_type' => 'MANUAL_CLAIMS',
                'is_active' => true,
                'is_automatic' => false,
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We don't reverse this as it's a data migration
        // The data should remain as is
    }
}
