<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebFormMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_form_messages', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('web_form_id')->unsigned();
            $table->foreign('web_form_id')->references('id')
                ->on('web_forms')->onDelete('cascade');

            $table->integer('user_id')->unsigned()->nullable();;
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade');

            $table->mediumText('data');

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
        Schema::drop('web_form_messages');
    }
}
