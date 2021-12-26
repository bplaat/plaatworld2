<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjectObjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('object_object', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_object_id');
            $table->unsignedBigInteger('object_id');
            $table->string('name');
            $table->double('position_x');
            $table->double('position_y');
            $table->double('position_z');
            $table->double('rotation_x');
            $table->double('rotation_y');
            $table->double('rotation_z');
            $table->double('scale_x');
            $table->double('scale_y');
            $table->double('scale_z');
            $table->timestamps();

            $table->foreign('parent_object_id')
                ->references('id')
                ->on('objects');

            $table->foreign('object_id')
                ->references('id')
                ->on('objects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('object_object');
    }
}
