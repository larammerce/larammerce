<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeliveryTimesToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("invoices", function(Blueprint $table){
            $table->date('delivery_date')->nullable();
            $table->time('delivery_start_time')->nullable();
            $table->time('delivery_finish_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("invoices", function(Blueprint $table){
            $table->dropColumn("delivery_date");
            $table->dropColumn("delivery_start_time");
            $table->dropColumn("delivery_finish_time");
        });
    }
}
