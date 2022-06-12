<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsToCustomerCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("customer_carts", function(Blueprint $table){
            $table->timestamps();
            $table->timestamp("customer_notified_at")->nullable();
            $table->timestamp("customer_viewed_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("customer_carts", function(Blueprint $table){
            $table->dropColumn("created_at");
            $table->dropColumn("updated_at");
            $table->dropColumn("customer_notified_at");
            $table->dropColumn("customer_viewed_at");
        });
    }
}
