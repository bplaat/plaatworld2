<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTauntsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taunts', function (Blueprint $table) {
            $table->id();
            $table->string('taunt');
            $table->string('text_en');
            $table->unsignedBigInteger('sound_id')->nullable();
            $table->boolean('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sound_id')
                ->references('id')
                ->on('sounds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taunts');
    }
}
