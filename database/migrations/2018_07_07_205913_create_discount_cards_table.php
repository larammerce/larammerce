<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_cards', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('discount_group_id')->unsigned();
            $table->foreign('discount_group_id')->references('id')
                ->on('discount_groups')->onDelete('cascade');
            $table->integer('customer_user_id')->unsigned()->nullable();
            $table->foreign('customer_user_id')->references('id')
                ->on('customer_users')->onDelete('cascade');
            $table->integer('invoice_id')->unsigned()->nullable();
            $table->foreign('invoice_id')->references('id')
                ->on('invoices')->onDelete('cascade');
            $table->string('client_ip')->nullable();
            $table->string('code')->nullable()->unique();

            $table->boolean('is_notified')->default(false);

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
        Schema::dropIfExists('discount_cards');
    }
}
