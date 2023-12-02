<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCrmOpportunityFieldsToCustomerUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('customer_users', function (Blueprint $table) {
            $table->boolean("crm_must_push_op")->default(false);
            $table->string("crm_op_id")->nullable()->index();
            $table->timestamp("crm_op_created_at")->nullable();
            $table->timestamp("crm_op_updated_at")->nullable();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string("crm_op_id")->nullable()->index();
            $table->string("crm_invoice_id")->nullable()->index();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string("crm_payment_id")->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('customer_users', function (Blueprint $table) {
            $table->dropColumn("crm_op_id");
            $table->dropColumn("crm_op_created_at");
            $table->dropColumn("crm_op_updated_at");
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn("crm_op_id");
            $table->dropColumn("crm_invoice_id");
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn("crm_payment_id");
        });


    }
}
