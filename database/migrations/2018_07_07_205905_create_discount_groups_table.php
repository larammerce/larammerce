<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_groups', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title');
            $table->string('prefix')->nullable()->default(null);
            $table->string('postfix')->nullable()->default(null);
            $table->boolean('is_assigned')->default(false);
            $table->boolean('is_percentage')->default(false);
            $table->dateTime('expiration_date')->nullable()->default(null);
            $table->boolean('is_active')->default(true);
            $table->bigInteger('value');
            $table->integer('max_amount_supported')->default(0);
            $table->integer('min_invoice_supported')->default(0);

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
        Schema::dropIfExists('discount_groups');
    }
}
