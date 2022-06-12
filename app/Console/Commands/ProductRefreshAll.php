<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/29/18
 * Time: 2:15 PM
 */

namespace App\Console\Commands;


use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

class ProductRefreshAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:refresh-all';

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
        Product::chunk(100, function ($products) {
            foreach ($products as $product) {
                $this->output->write("updating product {$product->id} : ");

                if ($product->model_id == null)
                    $product->model_id = $product->id;

                try {
                    $product->save();
                    $this->output->writeLn("[<fg=green>✔</>]");
                } catch (QueryException $e) {
                    $product->code = "dup_{$product->code}";
                    $product->save();
                }

                $directory = $product->directory;
                $directories = $directory->getParentDirectoriesRecursive();
                $directory_ids = array_map(function ($iter_dir) {
                    return $iter_dir->id;
                }, $directories);
                $directory_ids_str = join(", ", $directory_ids);
                $current_directory_ids = $product->directories()->pluck("id")->toArray();
                $current_directory_ids_str = join(", ", $current_directory_ids);
                $ids_should_be_added = array_diff($directory_ids, $current_directory_ids);

                if (count($ids_should_be_added) > 0) {
                    $this->info("Attach product {$product->id} to directories [{$directory_ids_str}]; current directories is [{$current_directory_ids_str}]");
                    $product->directories()->syncWithoutDetaching($directory_ids);
                }

            }
        });
        return 0;
    }
}
