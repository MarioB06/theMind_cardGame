<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameParticipantsTable extends Migration
{
    public function up()
    {
        Schema::create('game_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
    
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('card_number_1')->nullable();
            $table->integer('card_number_2')->nullable();
            $table->boolean('card_1_played')->default(false);
            $table->boolean('card_2_played')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_participants');
    }
}
