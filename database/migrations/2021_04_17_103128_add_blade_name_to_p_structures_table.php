<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBladeNameToPStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("p_structures", function (Blueprint $table) {
            $table->string("blade_name")->default("product-single");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("p_structures", function (Blueprint $table) {
            $table->dropColumn("blade_name");
        });
    }
}
