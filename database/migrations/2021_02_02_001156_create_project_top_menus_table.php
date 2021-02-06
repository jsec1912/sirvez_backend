<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTopMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_top_menus', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('menu');
            $table->string('quote');
            $table->string('install');
            $table->string('signoff');
            $table->string('archive');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_top_menus');
    }
}
