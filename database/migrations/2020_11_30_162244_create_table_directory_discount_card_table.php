<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDirectoryDiscountCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directory_discount_card', function (Blueprint $table) {
            $table->unsignedInteger('directory_id');
            $table->foreign('directory_id')->references('id')->on('directories')->onDelete('cascade');
            $table->unsignedInteger('discount_card_id');
            $table->foreign('discount_card_id')->references('id')->on('discount_cards')->onDelete('cascade');
            $table->primary(['directory_id', 'discount_card_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directories_discount_cards');
    }
}
