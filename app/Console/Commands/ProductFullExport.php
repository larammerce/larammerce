<?php


namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\ProductStructure;

class ProductFullExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:full-export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $result = [];
        foreach (ProductStructure::all() as $product_structure) {
            foreach ($product_structure->products as $product) {
                $tmp_product = [];
                $tmp_product["title"] = $product->title;
                $tmp_product["latest_price"] = $product->latest_price;
                $tmp_product["previous_price"] = $product->previous_price;
                $tmp_product["code"] = $product->code;
                foreach ($product->attributeValues as $attribute_value) {
                    $key = $attribute_value->key;
                    if (!key_exists("{$key->title}", $tmp_product)) {
                        $tmp_product["{$key->title}"] = "";
                    }

                    $tmp_product["{$key->title}"] .= $attribute_value->name . ", ";
                }

                $result[] = $tmp_product;
            }

            $file = fopen(base_path("data/tmp/export_product/all.json"), "w");
            fwrite($file, json_encode($result));
            fclose($file);
        }
        return 0;
    }
}
