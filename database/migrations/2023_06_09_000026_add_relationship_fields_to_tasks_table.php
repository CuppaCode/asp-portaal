<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToTasksTable extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'user_fk_8464718')->references('id')->on('users');
            $table->unsignedBigInteger('claim_id')->nullable();
            $table->foreign('claim_id', 'claim_fk_8491245')->references('id')->on('claims');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id', 'team_fk_8491282')->references('id')->on('teams');
        });
    }
}
