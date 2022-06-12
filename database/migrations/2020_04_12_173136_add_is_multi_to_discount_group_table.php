<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsMultiToDiscountGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("discount_groups", function(Blueprint $table) {
            $table->boolean("is_multi")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("discount_groups", function(Blueprint $table){
            $table->dropColumn("is_multi");
        });
    }
}
