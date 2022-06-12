<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRobotTxtRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('robot_txt_records', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('type')->default(0);
            $table->string('user_agent');
            $table->string('permission');
            $table->string('url');
            $table->index(['url', 'user_agent']);

            $table->integer('modified_url_id')->unsigned()->nullable();
            $table->foreign('modified_url_id')->references('id')
                ->on('modified_urls')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('robot_txt_records');
    }
}
