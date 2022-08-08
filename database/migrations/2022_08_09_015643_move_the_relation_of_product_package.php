<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveTheRelationOfProductPackage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("products", function (Blueprint $table) {
            $table->boolean("is_package")->default(false);
            $table->dropForeign("products_package_id_foreign");
            $table->dropColumn("package_id");
        });

        Schema::table("product_packages", function (Blueprint $table) {
            $table->unsignedInteger("product_id");
            $table->foreign("product_id")->references("id")->on("products")
                ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("products", function (Blueprint $table) {
            $table->dropColumn("is_package");
            $table->unsignedInteger("product_id");
            $table->foreign("package_id")->references("id")->on("product_packages")
                ->onDelete("cascade");
        });

        Schema::table("product_packages", function (Blueprint $table) {
            $table->dropForeign("product_packages_product_id_foreign");
            $table->dropColumn("product_id");
        });
    }
}
