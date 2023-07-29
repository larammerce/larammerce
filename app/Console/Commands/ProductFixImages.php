<?php


namespace App\Console\Commands;

use App\Helpers\ImageHelper;
use App\Jobs\ImageResizer;
use App\Models\ProductImage;
use Illuminate\Console\Command;

class ProductFixImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-image';

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
        /*$product = Product::find(2527);
        $product_images = $product->images;*/

        $product_images = ProductImage::all();
        $config = ImageHelper::getImageConfig('product');

        $counter = 0;
        foreach ($product_images as $product_image) {
            $original_path = ImageHelper::getImage($product_image, 'original');
            $path_parts = explode("/", $original_path);
            unset($path_parts[4]);
            $destination_path = join("/", $path_parts);

            $original_path = public_path() . $original_path;
            foreach ($config as $key => $value) {

                if ($key != 'ratio') {
                    $image_path = ImageHelper::getImage($product_image, $key);
                    if (strpos($image_path, $key) === false) {
                        $this->info($image_path);
                        $job = new ImageResizer(
                            $original_path,
                            $destination_path,
                            $key,
                            $value["width"],
                            $value["height"]);
                        dispatch($job);

                        $counter += 1;

                    }
                }
            }
        }

        $this->info("there was {$counter} product images to be resized.");
    }
}