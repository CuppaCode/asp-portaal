<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleOppositesTable extends Migration
{
    public function up()
    {
        Schema::create('vehicle_opposites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('plates')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
