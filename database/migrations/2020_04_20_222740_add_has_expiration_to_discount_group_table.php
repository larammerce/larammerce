<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHasExpirationToDiscountGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("discount_groups", function (Blueprint $table) {
            $table->boolean("has_expiration")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("discount_groups", function (Blueprint $table) {
            $table->dropColumn("has_expiration");
        });
    }
}
