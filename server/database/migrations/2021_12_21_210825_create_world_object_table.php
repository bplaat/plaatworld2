<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorldObjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('world_object', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('world_id');
            $table->unsignedBigInteger('object_id');
            $table->double('position_x');
            $table->double('position_y');
            $table->double('position_z');
            // $table->double('rotation_x');
            // $table->double('rotation_y');
            // $table->double('rotation_z');
            $table->timestamps();

            $table->foreign('world_id')
                ->references('id')
                ->on('worlds');

            $table->foreign('object_id')
                ->references('id')
                ->on('objects');

            $table->unique(['world_id', 'object_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('world_object');
    }
}
