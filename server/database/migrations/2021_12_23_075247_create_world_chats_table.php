<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorldChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('world_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('world_id');
            $table->unsignedBigInteger('user_id');
            $table->string('message');
            $table->timestamps();

            $table->foreign('world_id')
                ->references('id')
                ->on('worlds');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('world_chats');
    }
}
