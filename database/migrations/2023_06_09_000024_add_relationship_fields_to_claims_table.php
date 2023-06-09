<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToClaimsTable extends Migration
{
    public function up()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id', 'company_fk_8464692')->references('id')->on('companies');
            $table->unsignedBigInteger('injury_office_id')->nullable();
            $table->foreign('injury_office_id', 'injury_office_fk_8464721')->references('id')->on('injury_offices');
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->foreign('vehicle_id', 'vehicle_fk_8464808')->references('id')->on('vehicles');
            $table->unsignedBigInteger('vehicle_opposite_id')->nullable();
            $table->foreign('vehicle_opposite_id', 'vehicle_opposite_fk_8464809')->references('id')->on('vehicle_opposites');
            $table->unsignedBigInteger('recovery_office_id')->nullable();
            $table->foreign('recovery_office_id', 'recovery_office_fk_8491232')->references('id')->on('recovery_offices');
            $table->unsignedBigInteger('expertise_office_id')->nullable();
            $table->foreign('expertise_office_id', 'expertise_office_fk_8491241')->references('id')->on('expertise_offices');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id', 'team_fk_8491276')->references('id')->on('teams');
        });
    }
}
