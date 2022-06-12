<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductPreviousPrice extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Product::all() as $product) {
            $prev = $product->prices()->where('value', '!=', $product->latest_price)
                ->orderBy('id', 'DESC')->first();

            if ($prev != null) {
                $product->previous_price = $prev->value;
                $product->save();
            } else {
                echo "product with id {$product->id} can not be updated, there is no prev price !\n";
                echo "===========================================================================\n";
                foreach ($product->prices as $price) {
                    echo "price: {$price->value}\n";
                }
            }
        }
    }
}
