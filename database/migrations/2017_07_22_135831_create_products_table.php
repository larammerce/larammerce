<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->bigInteger('latest_price');
            $table->text('extra_properties');
            $table->text('description');

            // rating columns
            $table->float('average_rating')->default(0);
            $table->integer('rating_number')->default(0);

            $table->integer('directory_id')->unsigned();
            $table->foreign('directory_id')->references('id')
                ->on('directories')->onDelete('cascade');
            $table->integer('p_structure_id')->unsigned();
            $table->foreign('p_structure_id')->references('id')
                ->on('p_structures')->onDelete('cascade');

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
        Schema::drop('products');
    }
}
