<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerMetaCategoryFieldsToCustomerCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("customer_carts", function (Blueprint $table) {
            $table->unsignedBigInteger("cmi_id")->nullable();
            $table->foreign("cmi_id")->references("id")->on("customer_meta_items")
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("customer_carts", function (Blueprint $table) {
            $table->dropColumn("cmi_id");
        });
    }
}
