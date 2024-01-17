<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToMailTemplatesTable extends Migration
{
    public function up()
    {
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id', 'team_fk_9404825')->references('id')->on('teams');
        });
    }
}
