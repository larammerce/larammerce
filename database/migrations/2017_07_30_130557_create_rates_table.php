<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('object_id')->unsigned();
            $table->string('object_type');

            $table->integer('rating');
            $table->text('comment')->nullable();

            $table->integer('customer_user_id')->unsigned()->nullable();
            $table->foreign('customer_user_id')->references('id')
                ->on('customer_users')->onDelete('set null');

            $table->boolean('is_accepted')->default(false);

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
        Schema::drop('rates');
    }
}
