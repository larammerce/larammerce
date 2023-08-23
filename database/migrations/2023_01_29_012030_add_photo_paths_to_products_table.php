<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AddPhotoPathsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // Update the php ini memory limit to unlimited
        ini_set('memory_limit', '-1');

        Schema::table('products', function (Blueprint $table) {
            $table->string("main_photo")->nullable();
            $table->string("secondary_photo")->nullable();
        });

        // Bellow process gets all products and updates their main and secondary photo paths, but it goes slow after 100, 150 records to update, update the code to do it fast until the end
        \App\Models\Product::with("images")->chunk(500,
            /**
             * @param Product[] $products
             */
            function (Collection|array $products) {
                foreach ($products as $product) {
                    echo "Updating product {$product->id} image addresses." . PHP_EOL;
                    $main_photo_path = $product->getMainPhoto()?->getImagePath();
                    $secondary_photo_path = $product->getSecondaryPhoto()?->getImagePath();
                    $product->update([
                        "main_photo" => $main_photo_path,
                        "secondary_photo" => $secondary_photo_path,
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
