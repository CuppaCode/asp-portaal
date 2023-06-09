<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverVehiclePivotTable extends Migration
{
    public function up()
    {
        Schema::create('driver_vehicle', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id');
            $table->foreign('vehicle_id', 'vehicle_id_fk_8597345')->references('id')->on('vehicles')->onDelete('cascade');
            $table->unsignedBigInteger('driver_id');
            $table->foreign('driver_id', 'driver_id_fk_8597345')->references('id')->on('drivers')->onDelete('cascade');
        });
    }
}
