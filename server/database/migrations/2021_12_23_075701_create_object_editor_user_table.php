<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjectEditorUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('object_editor_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('object_id');
            $table->unsignedBigInteger('user_id');
            $table->double('camera_position_x');
            $table->double('camera_position_y');
            $table->double('camera_position_z');
            $table->double('camera_rotation_x');
            $table->double('camera_rotation_y');
            $table->double('camera_rotation_z');
            $table->unsignedBigInteger('selected_object_id')->nullable();
            $table->timestamps();

            $table->foreign('object_id')
                ->references('id')
                ->on('objects');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('selected_object_id')
                ->references('id')
                ->on('objects');

            $table->unique(['object_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('object_editor_user');
    }
}
