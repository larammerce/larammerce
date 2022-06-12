<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameDiscountGroupMinInvoiceSupportedField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("discount_groups", function (Blueprint $table) {
            $table->renameColumn("min_invoice_supported", "min_amount_supported");
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
            $table->renameColumn("min_amount_supported", "min_invoice_supported");
        });
    }
}
