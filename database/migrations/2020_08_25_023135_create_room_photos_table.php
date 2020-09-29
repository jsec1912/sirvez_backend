<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_photos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('room_id');
            $table->string('img_name');
            $table->string('user_id')->nullable();
            $table->string('off_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_photos');
    }
}
