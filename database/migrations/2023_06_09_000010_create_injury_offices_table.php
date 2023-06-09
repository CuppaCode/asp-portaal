<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInjuryOfficesTable extends Migration
{
    public function up()
    {
        Schema::create('injury_offices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('identifier')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
