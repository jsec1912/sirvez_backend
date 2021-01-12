<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_chats', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('project_id');
            $table->string('sender_id');
            $table->string('reciever_id');
            $table->string('message');
            $table->string('is_read')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_chats');
    }
}

