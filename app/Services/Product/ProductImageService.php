<?php

namespace App\Services\Product;

use App\Models\ProductImage;

class ProductImageService {
    public static function setImageAsMain(ProductImage $image): void {
        if ($image->is_main)
            return;

        $product = $image->product;
        $product->images()->update([
            "is_main" => false
        ]);
        $image->update([
            "is_main" => true
        ]);
        $product->update([
            "main_photo" => $image->getImagePath()
        ]);
    }

    public static function setImageAsSecondary(ProductImage $image): void {
        $product = $image->product;
        $product->images()->update([
            "is_secondary" => false
        ]);
        $image->update([
            "is_secondary" => true
        ]);
        $product->update([
            "secondary_photo" => $image->getImagePath()
        ]);
    }
}
