<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountGroupProductFilterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("discount_group_product_filter", function (Blueprint $table) {
            $table->unsignedInteger("product_filter_id");
            $table->foreign("product_filter_id")->references("id")->on("product_filters")->onDelete("cascade");

            $table->unsignedInteger("discount_group_id");
            $table->foreign("discount_group_id")->references("id")->on("discount_groups")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("discount_group_product_filter");
    }
}
