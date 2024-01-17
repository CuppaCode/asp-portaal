<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimsTable extends Migration
{
    public function up()
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('assign_self')->default(0)->nullable();
            $table->string('subject');
            $table->string('claim_number');
            $table->string('status');
            $table->string('injury');
            $table->string('contact_lawyer')->nullable();
            $table->date('date_accident')->nullable();
            $table->string('recoverable_claim')->nullable();
            $table->string('injury_other')->nullable();
            $table->string('opposite_type')->nullable();
            $table->string('damaged_part')->nullable();
            $table->string('damage_origin')->nullable();
            $table->string('damaged_area')->nullable();
            $table->string('damaged_part_opposite')->nullable();
            $table->string('damage_origin_opposite')->nullable();
            $table->string('damaged_area_opposite')->nullable();
            $table->decimal('damage_costs', 15, 2)->nullable();
            $table->decimal('recovery_costs', 15, 2)->nullable();
            $table->decimal('replacement_vehicle_costs', 15, 2)->nullable();
            $table->decimal('expert_costs', 15, 2)->nullable();
            $table->decimal('other_costs', 15, 2)->nullable();
            $table->decimal('deductible_excess_costs', 15, 2)->nullable();
            $table->decimal('insurance_costs', 15, 2)->nullable();
            $table->boolean('expert_report_is_in')->default(0)->nullable();
            $table->datetime('requested_at');
            $table->datetime('report_received_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
