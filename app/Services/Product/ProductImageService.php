<?php

namespace App\Services\Product;

use App\Models\ProductImage;
use App\Utils\Common\ImageService;

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
            "main_photo" => ImageService::getImage($image, "preview")
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
            "secondary_photo" => ImageService::getImage($image, "preview")
        ]);
    }
}