<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditFinRelationAttribute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_users', function (Blueprint $table) {
            $table->dropColumn("business_code");
            $table->text("fin_relation");
        });
        Schema::table('customer_users_legal_info', function (Blueprint $table) {
            $table->dropColumn("business_code");
            $table->text("fin_relation");
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->text("fin_relation");
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
