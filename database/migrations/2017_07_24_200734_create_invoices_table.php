<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_type');
            $table->integer('customer_user_id')->unsigned();
            $table->foreign('customer_user_id')->references('id')
                ->on('customer_users')->onDelete('cascade');
            $table->integer('customer_address_id')->unsigned();
            $table->foreign('customer_address_id')->references('id')
                ->on('customer_addresses')->onDelete('cascade');

            $table->bigInteger('sum')->default(0);
            $table->string('payment_id');
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
        Schema::drop('invoices');
    }
}
