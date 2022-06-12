<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerMetaCategoryFieldsToDirectoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("directories", function (Blueprint $table) {
            $table->unsignedInteger("cmc_id")->nullable();
            $table->foreign("cmc_id")->references("id")->on("customer_meta_categories")
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("directories", function (Blueprint $table) {
            $table->dropColumn("cmc_id");
        });
    }
}
