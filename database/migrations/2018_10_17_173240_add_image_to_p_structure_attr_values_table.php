<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageToPStructureAttrValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('p_structure_attr_values', function (Blueprint $table) {
            $table->string('image_path')->nullable();
            $table->string('image_alias')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('p_structure_attr_values', function (Blueprint $table) {
            $table->dropColumn('image_path');
            $table->dropColumn('image_alias');
        });
    }
}
