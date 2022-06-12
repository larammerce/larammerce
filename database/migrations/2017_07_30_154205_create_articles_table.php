<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('directory_id')->unsigned()->nullable();
            $table->foreign('directory_id')->references('id')
                ->on('directories')->onDelete('cascade');

            $table->integer('system_user_id')->unsigned()->nullable();
            $table->foreign('system_user_id')->references('id')
                ->on('system_users')->onDelete('cascade');

            $table->string('title');
            $table->text('short_content');
            $table->mediumText('full_text');
            $table->string('source')->nullable();
            $table->string('image_path')->nullable();

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
        Schema::drop('articles');
    }
}
