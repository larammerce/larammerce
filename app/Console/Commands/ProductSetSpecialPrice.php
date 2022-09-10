<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/29/18
 * Time: 2:15 PM
 */

namespace App\Console\Commands;


use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProductSetSpecialPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:set-special-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file_contents = file_get_contents(base_path("data/discount_products.json"));
        $data_rows = json_decode($file_contents, true);

        foreach ($data_rows as $data_row) {
            if (!isset($data_row["code"]) or !isset($data_row["percentage"]))
                continue;
            $product = Product::where("code", "{$data_row["code"]}")->first();

            if ($product == null)
                continue;

            $product->update([
                "latest_special_price" => ($product->latest_price * (100 - $data_row["percentage"]) / 100),
                "has_discount" => true
            ]);
        }

        return 0;
    }
}
