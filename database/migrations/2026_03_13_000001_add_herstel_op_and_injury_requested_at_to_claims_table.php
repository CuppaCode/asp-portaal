<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHerstelOpAndInjuryRequestedAtToClaimsTable extends Migration
{
    public function up()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->date('herstel_op')->nullable()->after('recovery_office_id');
            $table->date('injury_requested_at')->nullable()->after('injury_office_id');
        });
    }

    public function down()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn(['herstel_op', 'injury_requested_at']);
        });
    }
}
