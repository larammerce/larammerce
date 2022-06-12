<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBankDataToCustomerUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("customer_users", function (Blueprint $table) {
            $table->string("bank_account_card_number", 16)->nullable();
            $table->string("bank_account_uuid", 24)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("customer_users", function (Blueprint $table) {
            $table->dropColumn("bank_account_card_number", 16);
            $table->dropColumn("bank_account_uuid", 24);
        });
    }
}
