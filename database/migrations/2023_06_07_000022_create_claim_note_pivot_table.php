<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimNotePivotTable extends Migration
{
    public function up()
    {
        Schema::create('claim_note', function (Blueprint $table) {
            $table->unsignedBigInteger('note_id');
            $table->foreign('note_id', 'note_id_fk_8597400')->references('id')->on('notes')->onDelete('cascade');
            $table->unsignedBigInteger('claim_id');
            $table->foreign('claim_id', 'claim_id_fk_8597400')->references('id')->on('claims')->onDelete('cascade');
        });
    }
}
