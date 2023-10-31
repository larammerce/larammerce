<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCrmRelationToProperTables extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table("customer_users", function (Blueprint $table) {
            $table->string("crm_relation")
                ->index()
                ->nullable();
        });

        Schema::table("customer_users_legal_info", function (Blueprint $table) {
            $table->string("crm_relation")
                ->index()
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table("customer_users", function (Blueprint $table) {
            $table->dropColumn("crm_relation");
        });

        Schema::table("customer_users_legal_info", function (Blueprint $table) {
            $table->dropColumn("crm_relation");
        });
    }
}
