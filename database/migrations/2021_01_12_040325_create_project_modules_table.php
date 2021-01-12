<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_modules', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('project_id');
            $table->string('created_by');
            $table->string('task');
            $table->string('version');
            $table->string('schedule');
            $table->string('calendar');
            $table->string('chat');
            $table->string('product');
            $table->string('activity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_modules');
    }
}
