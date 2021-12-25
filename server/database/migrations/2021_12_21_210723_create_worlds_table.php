<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worlds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('width');
            $table->double('height');
            $table->double('spawn_position_x');
            $table->double('spawn_position_y');
            $table->double('spawn_position_z');
            $table->double('spawn_rotation_x');
            $table->double('spawn_rotation_y');
            $table->double('spawn_rotation_z');
            $table->unsignedBigInteger('sky_texture_id')->nullable();
            $table->boolean('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sky_texture_id')
                ->references('id')
                ->on('textures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('worlds');
    }
}
