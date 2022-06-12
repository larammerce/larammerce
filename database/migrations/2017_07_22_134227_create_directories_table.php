<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirectoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directories', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title');
            $table->string('url_part')->nullable();
            $table->string('url_full');
            $table->boolean('is_internal_link');
            $table->integer('priority');
            $table->integer('content_type');
            $table->boolean('has_web_page')->default(false);

            $table->integer('directory_id')->unsigned()->nullable();
            $table->foreign('directory_id')->references('id')
                ->on('directories')->onDelete('cascade');

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
        Schema::drop('directories');
    }
}
