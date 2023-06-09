<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverVehicleOppositePivotTable extends Migration
{
    public function up()
    {
        Schema::create('driver_vehicle_opposite', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_opposite_id');
            $table->foreign('vehicle_opposite_id', 'vehicle_opposite_id_fk_8597346')->references('id')->on('vehicle_opposites')->onDelete('cascade');
            $table->unsignedBigInteger('driver_id');
            $table->foreign('driver_id', 'driver_id_fk_8597346')->references('id')->on('drivers')->onDelete('cascade');
        });
    }
}
