<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductStructureAttrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_structure_attrs', function (Blueprint $table) {
            $table->integer('p_structure_id')->unsigned();
            $table->foreign('p_structure_id')->references('id')
                ->on('p_structures')->onDelete('cascade');

            $table->integer('p_structure_attr_key_id')->unsigned();
            $table->foreign('p_structure_attr_key_id')->references('id')
                ->on('p_structure_attr_keys')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('p_structure_attrs');
    }
}
