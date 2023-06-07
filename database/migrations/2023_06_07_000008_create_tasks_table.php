<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('task_number')->unique();
            $table->longText('description')->nullable();
            $table->datetime('deadline_at');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
