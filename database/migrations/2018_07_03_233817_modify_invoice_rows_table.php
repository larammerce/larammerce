<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyInvoiceRowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_rows', function (Blueprint $table){
            $table->dropColumn('discount_percentage');
            $table->bigInteger('discount_amount');
            $table->bigInteger('tax_amount');
            $table->bigInteger('toll_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
