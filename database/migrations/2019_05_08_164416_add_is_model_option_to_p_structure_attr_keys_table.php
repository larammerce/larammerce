<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIsModelOptionToPStructureAttrKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("p_structure_attr_keys", function(Blueprint $table){
            $table->boolean("is_model_option")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("p_structure_attr_keys", function(Blueprint $table){
            $table->dropColumn("is_model_option");
        });
    }
}
