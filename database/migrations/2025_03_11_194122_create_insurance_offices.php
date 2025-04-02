<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsuranceOfficesTable extends Migration
{
    public function up()
    {
        Schema::create('insurance_offices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('identifier')->nullable();
        });
    }
}
