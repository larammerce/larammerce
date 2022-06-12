<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDiscountCardIdToInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("invoices", function(Blueprint $table) {
            $table->integer("discount_card_id")->unsigned()->nullable();
            $table->foreign("discount_card_id")->references("id")
                ->on("discount_cards")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("invoices", function(Blueprint $table) {
            $table->dropColumn("discount_cart_id");
        });
    }
}
