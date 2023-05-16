<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToDriversTable extends Migration
{
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->foreign('vehicle_id', 'vehicle_fk_8464782')->references('id')->on('vehicles');
            $table->unsignedBigInteger('vehicle_opposite_id')->nullable();
            $table->foreign('vehicle_opposite_id', 'vehicle_opposite_fk_8464799')->references('id')->on('vehicle_opposites');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id', 'team_fk_8491285')->references('id')->on('teams');
        });
    }
}
