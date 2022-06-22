<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePStructureAttrValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_structure_attr_values', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('p_structure_attr_key_id')->unsigned();
            $table->foreign('p_structure_attr_key_id')->references('id')
                ->on('p_structure_attr_keys')->onDelete('cascade');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('p_structure_attr_values');
    }
}
