<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVersionControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('version_controls', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('version_name');
            $table->string('description');
            $table->string('tag');
            $table->string('version_number');
            $table->string('project_id');
            $table->string('room_id');
            $table->string('editable_link');
            $table->string('pdf_link');
            $table->string('created_by');
            $table->string('parent_id');
            $table->string('off_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('version_controls');
    }
}
