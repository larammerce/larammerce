<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCrmRelationToCustomerAndLegalInfoTables extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('customer_users', function (Blueprint $table) {
            $table->renameColumn('crm_relation', 'crm_lead_id');
            $table->string('crm_account_id')->index()->nullable();
        });

        Schema::table('customer_users_legal_info', function (Blueprint $table) {
            $table->string('crm_account_id')->index()->nullable();
            $table->dropColumn('crm_relation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('customer_users', function (Blueprint $table) {
            $table->renameColumn('crm_lead_id', 'crm_relation');
            $table->dropColumn('crm_account_id');
        });

        Schema::table('customer_users_legal_info', function (Blueprint $table) {
            $table->dropColumn('crm_account_id');
        });
    }
}
