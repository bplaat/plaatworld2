<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objects', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type');
            $table->string('name');
            $table->double('width');
            $table->double('height');
            $table->double('depth');
            $table->unsignedBigInteger('texture_id')->nullable();
            $table->integer('texture_repeat_x');
            $table->integer('texture_repeat_y');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedInteger('item_chance');
            $table->unsignedInteger('item_amount');
            $table->boolean('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('texture_id')
                ->references('id')
                ->on('textures');

            $table->foreign('item_id')
                ->references('id')
                ->on('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('objects');
    }
}
