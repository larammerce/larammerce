<?php

namespace App\Jobs\Product;

use App\Jobs\ImageResizer;
use App\Models\Product;
use App\Services\Product\ProductWatermarkService;
use App\Utils\CMS\Setting\ProductWatermark\ProductWatermarkSettingModel;
use App\Utils\Common\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ApplyProductWatermarkJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ProductWatermarkSettingModel $product_watermark_setting;
    private Product $product;
    private bool $is_force;

    public function __construct(Product $product, ProductWatermarkSettingModel $product_watermark_setting, bool $is_force = false) {
        $this->product = $product;
        $this->product_watermark_setting = $product_watermark_setting;
        $this->is_force = $is_force;
    }

    public function handle(ProductWatermarkService $image_watermark_service) {
        if ($this->is_force or $this->product->watermark_uuid !== $this->product_watermark_setting->getWatermarkUUID()) {
            foreach ($this->product->images as $product_image) {
                $image_watermark_service->applyWatermark($product_image->getImagePath(), $this->product_watermark_setting);

                foreach (ImageService::getImageConfig("product") as $key => $value) {
                    if ($key != 'ratio' and in_array(strtolower($product_image->extension), ['jpg', 'png', 'webp'])) {
                        $job = new ImageResizer(public_path($product_image->getImagePath()), $product_image->path, $key, $value["width"], $value["height"]);
                        $job->handle();
                    }
                }
            }
        }
    }
}
