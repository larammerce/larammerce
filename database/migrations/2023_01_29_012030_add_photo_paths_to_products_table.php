<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AddPhotoPathsToProductsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('products', function (Blueprint $table) {
            $table->string("main_photo")->nullable();
            $table->string("secondary_photo")->nullable();
        });

        \App\Models\Product::chunk(100,
            /**
             * @param Product[] $products
             */
            function (Collection|array $products) {
                foreach ($products as $product) {
                    $product->update([
                        "main_photo" => $product->getMainPhoto()->getImagePath(),
                        "secondary_photo" => $product->getSecondaryPhoto()->getImagePath(),
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn("main_photo");
            $table->dropColumn("secondary_photo");
        });
    }
}
