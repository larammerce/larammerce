<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccessoryForKeyToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger("accessory_for")->nullable()->default(null);
        });

        Product::where("is_accessory", true)->chunk(100,
            /**
             * @param Product[] $products
             */
            function ($products) {
                foreach ($products as $product) {
                    $product->update([
                        "accessory_for" => $product->model_id,
                        "model_id" => $product->id
                    ]);
                }
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn("accessory_for");
        });
    }
}
