<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceIdColumnToCouponsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedInteger("invoice_id")->nullable();
            $table->foreign("invoice_id")->references("id")
                ->on("invoices")->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign("coupons_invoice_id_foreign");
            $table->dropColumn("invoice_id");
        });
    }
}
