<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxAndTollToProductsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedTinyInteger("tax_percentage")->default(0);
            $table->unsignedTinyInteger("toll_percentage")->default(0);
            $table->boolean("is_tax_included")->default(false);
        });

        Schema::table('directories', function (Blueprint $table) {
            $table->unsignedTinyInteger("tax_percentage")->default(0);
            $table->unsignedTinyInteger("toll_percentage")->default(0);
            $table->boolean("is_tax_included")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn("tax_percentage");
            $table->dropColumn("toll_percentage");
            $table->dropColumn("is_tax_included");
        });

        Schema::table("directories", function (Blueprint $table) {
            $table->dropColumn("tax_percentage");
            $table->dropColumn("toll_percentage");
            $table->dropColumn("is_tax_included");
        });
    }
}
