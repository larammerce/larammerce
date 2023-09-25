<?php

namespace App\Console\Commands;

use App\Jobs\Product\ProductImportFromJsonFile;
use Illuminate\Console\Command;

class ProductImport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected array $colors;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->colors = [];
    }

    public function handle() {
        $products_root_dir = public_path("uploads/data/");
        $result_array = [];
        foreach (scandir($products_root_dir) as $index => $product_sub_dir) {
            $sub_dir_full_path = $products_root_dir . $product_sub_dir;

            if (!is_dir($sub_dir_full_path) or in_array($product_sub_dir, [".", ".."])) {
                echo "D";
                continue;
            }

            $data_file_path = $sub_dir_full_path . "/data.json";
            if (!is_file($data_file_path)) {
                echo "N";
                continue;
            }

            $data = json_decode(file_get_contents($data_file_path));
            if (!isset($data->priority) or !isset($data->code) or !isset($data->ref) or !isset($data->urls) or !isset($data->categories)) {
                echo "X";
                continue;
            }

            $result_array[$sub_dir_full_path] = $data->priority;
            echo ".";
        }

        asort($result_array);

        foreach ($result_array as $path => $priority) {
            dispatch(new ProductImportFromJsonFile(2, $path . "/data.json", $path));
        }

        return 0;
    }


}
