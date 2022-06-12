<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_records', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('web_form_id')->unsigned();
            $table->foreign('web_form_id')->references('id')
                ->on('web_forms')->onDelete('cascade');

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
        Schema::drop('form_records');
    }
}
