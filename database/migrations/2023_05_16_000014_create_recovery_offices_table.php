<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecoveryOfficesTable extends Migration
{
    public function up()
    {
        Schema::create('recovery_offices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('identifier')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
