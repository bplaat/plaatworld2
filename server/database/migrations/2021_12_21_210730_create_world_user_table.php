<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorldUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('world_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('world_id');
            $table->unsignedBigInteger('user_id');
            $table->double('position_x');
            $table->double('position_y');
            $table->double('position_z');
            $table->double('rotation_x');
            $table->double('rotation_y');
            $table->double('rotation_z');
            $table->timestamps();

            $table->foreign('world_id')
                ->references('id')
                ->on('worlds');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->unique(['world_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('world_user');
    }
}
