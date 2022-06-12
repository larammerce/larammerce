<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductAttrAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_attr_assignments', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')
                ->on('products')->onDelete('cascade');

            $table->integer('p_structure_attr_key_id')->unsigned();
            $table->foreign('p_structure_attr_key_id')->references('id')
                ->on('p_structure_attr_keys')->onDelete('cascade');

            $table->integer('p_structure_attr_value_id')->unsigned();
            $table->foreign('p_structure_attr_value_id')->references('id')
                ->on('p_structure_attr_values')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('p_attr_assignments');
    }
}
