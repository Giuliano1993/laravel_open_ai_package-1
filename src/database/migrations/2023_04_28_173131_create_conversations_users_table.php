<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations_users', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBiginteger('user_id')->unsigned();
            $table->unsignedBiginteger('conversation_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users');

            $table->foreign('conversation_id')->references('id')->on('conversations');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversations_users');
    }
};
