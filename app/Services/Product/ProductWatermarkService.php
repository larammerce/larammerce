<?php

namespace App\Services\Product;

use App\Enums\Product\ProductWatermarkPosition;
use App\Utils\CMS\Setting\ProductWatermark\ProductWatermarkSettingModel;
use Intervention\Image\Facades\Image;

class ProductWatermarkService {
    public function applyWatermark(string $product_image_path, ProductWatermarkSettingModel $watermark_setting): void {
        $product_image_path = public_path($product_image_path);
        $watermark_image_path = public_path($watermark_setting->getWatermarkImage());

        if (!is_file($product_image_path) or !is_file($watermark_image_path)) {
            return;
        }

        // Path to the original image
        $original_path = pathinfo($product_image_path, PATHINFO_DIRNAME) . '/' .
            pathinfo($product_image_path, PATHINFO_FILENAME) .
            '_original.' .
            pathinfo($product_image_path, PATHINFO_EXTENSION);

        // If an original file doesn't exist, create it.
        if (!file_exists($original_path)) {
            copy($product_image_path, $original_path);
        }

        // Work with the original image
        $image = Image::make($original_path);

        // Load the watermark image
        $watermark = Image::make($watermark_image_path);

        // Calculate watermark dimensions
        $watermark_width = ($image->width() * $watermark_setting->getWatermarkSizePercentage()) / 100;
        $watermark->resize($watermark_width, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        // Calculate position
        $position = $this->calculatePosition($image, $watermark, $watermark_setting->getWatermarkPosition());

        // Apply watermark and save it as a new image
        $image->insert($watermark, $position['align'], $position['x'], $position['y']);
        $image->save($product_image_path);
    }

    private function calculatePosition($image, $watermark, $position): array {
        $width = $image->width();
        $height = $image->height();
        $watermark_width = $watermark->width();
        $watermark_height = $watermark->height();

        // Calculate 1.5% margin
        $x_margin = intval((1.5 / 100) * $width);
        $y_margin = intval((1.5 / 100) * $height);

        return match ($position) {
            ProductWatermarkPosition::TOP_RIGHT => ['align' => 'top-right', 'x' => $x_margin, 'y' => $y_margin],
            ProductWatermarkPosition::BOTTOM_LEFT => ['align' => 'bottom-left', 'x' => $x_margin, 'y' => $y_margin],
            ProductWatermarkPosition::BOTTOM_RIGHT => ['align' => 'bottom-right', 'x' => $x_margin, 'y' => $y_margin],
            ProductWatermarkPosition::CENTER => ['align' => 'center', 'x' => 0, 'y' => 0],
            ProductWatermarkPosition::TOP_CENTER => ['align' => 'top', 'x' => $width / 2 - $watermark_width / 2, 'y' => $y_margin],
            ProductWatermarkPosition::BOTTOM_CENTER => ['align' => 'bottom', 'x' => $width / 2 - $watermark_width / 2, 'y' => $y_margin],
            ProductWatermarkPosition::LEFT_CENTER => ['align' => 'left', 'x' => $x_margin, 'y' => $height / 2 - $watermark_height / 2],
            ProductWatermarkPosition::RIGHT_CENTER => ['align' => 'right', 'x' => $x_margin, 'y' => $height / 2 - $watermark_height / 2],
            default => ['align' => 'top-left', 'x' => $x_margin, 'y' => $y_margin],
        };
    }
}
