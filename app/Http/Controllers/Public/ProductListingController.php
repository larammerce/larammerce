<?php

namespace App\Http\Controllers\Public;

use App\Models\Product;

class ProductListingController {
    public function emalls() {
        $products = Product::query()->visible()->isActive()->paginate(100);
        return view('defaults.product-listing.emalls', compact('products'));
    }
}
