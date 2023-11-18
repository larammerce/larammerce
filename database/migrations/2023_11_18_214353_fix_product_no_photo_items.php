<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class FixProductNoPhotoItems extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        \App\Models\Product::chunk(1000,
            /**
             * @param Product[]|Collection $products
             */
            function (array|Collection $products) {
                foreach ($products as $product) {
                    $main_image = $product->images()->main()->first();
                    $secondary_image = $product->images()->secondary()->first();

                    $main_image_path = $main_image?->getImagePath();
                    $secondary_image_path = $secondary_image?->getImagePath();

                    if ($main_image_path === null) {
                        $product->update([
                            "main_photo" => null
                        ]);
                    }

                    if ($secondary_image_path === null) {
                        $product->update([
                            "secondary_photo" => null
                        ]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }
}
