<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_pages', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('directory_id')->unsigned()->nullable();
            $table->foreign('directory_id')->references('id')
                ->on('directories')->onDelete('cascade');

            $table->string('blade_name');
            $table->mediumText('data');
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
        Schema::drop('web_pages');
    }
}
