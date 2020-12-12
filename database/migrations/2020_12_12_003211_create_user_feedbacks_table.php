<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('feedback_email');
            $table->string('feedback_type');
            $table->string('feedback_msg');
            $table->string('user_id');
            $table->string('is_read');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_feedbacks');
    }
}
