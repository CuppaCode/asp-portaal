<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subject');
            $table->longText('body');
            $table->json('recipients'); // Required field for email addresses
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->string('reply_to')->nullable();
            $table->string('status')->default('draft'); // draft, ready, scheduled, sent, failed
            $table->timestamp('sent_at')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('mail_template_id')->nullable();
            $table->unsignedInteger('team_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('claim_mailing', function (Blueprint $table) {
            $table->unsignedBigInteger('mailing_id');
            $table->foreign('mailing_id')->references('id')->on('mailings')->onDelete('cascade');
            $table->unsignedBigInteger('claim_id');
            $table->foreign('claim_id')->references('id')->on('claims')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('claim_mailing');
        Schema::dropIfExists('mailings');
    }
}
