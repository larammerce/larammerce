<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLegalCustomerUsersInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_users_legal_info', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('customer_user_id')->unsigned();
            $table->foreign('customer_user_id')->references('id')->on('customer_users')->onDelete('cascade');

            $table->string('company_name');
            $table->string('economical_code');
            $table->string('national_id');
            $table->string('registration_code');
            $table->string('company_phone');

            $table->integer('state_id')->unsigned()->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');

            $table->integer('city_id')->unsigned()->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('customer_users_legal_info');
    }
}
