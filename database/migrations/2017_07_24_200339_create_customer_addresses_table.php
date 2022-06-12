<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('customer_user_id')->unsigned();
            $table->foreign('customer_user_id')->references('id')
                ->on('customer_users')->onDelete('cascade');
            $table->string('name');
            $table->integer('state_id')->unsigned();
            $table->foreign('state_id')->references('id')
                ->on('states')->onDelete('cascade');
            $table->integer('city_id')->unsigned();
            $table->foreign('city_id')->references('id')
                ->on('cities')->onDelete('cascade');
            $table->integer('district_id')->unsigned()->nullable();
            $table->foreign('district_id')->references('id')
                ->on('districts')->onDelete('cascade');

            $table->string('phone_number');
            $table->string('zipcode')->unique();
            $table->text('superscription');
            $table->string('transferee_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('customer_addresses');
    }
}
