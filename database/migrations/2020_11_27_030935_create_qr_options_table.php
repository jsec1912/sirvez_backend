<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQrOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qr_options', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('sirvez_logo');
            $table->string('client_name');
            $table->string('install_date');
            $table->string('website');
            $table->string('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qr_options');
        $table->string('company_logo');
    }
}
