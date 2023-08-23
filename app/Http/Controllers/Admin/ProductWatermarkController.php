<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\Product\ApplyProductWatermarkJob;
use App\Models\Product;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\ProductWatermark\ProductWatermarkSettingService;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\History;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * @role(enabled=true)
 */
class ProductWatermarkController extends BaseController {

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application {
        $watermark_setting = ProductWatermarkSettingService::getRecord();
        return view("admin.pages.product-watermark.edit")->with([
            "watermark_setting" => $watermark_setting
        ]);
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(
     *     watermark_position="required|in:". App\Enums\Product\ProductWatermarkPosition::stringValues(),
     *     watermark_size_percentage="required|integer|min:1|max:100",
     *     watermark_image="image"
     * )
     */
    public function update(Request $request): \Illuminate\Http\RedirectResponse {
        $watermark_setting = ProductWatermarkSettingService::getRecord();
        $watermark_setting->setWatermarkPosition($request->get("watermark_position"));
        $watermark_setting->setWatermarkSizePercentage($request->get("watermark_size_percentage"));
        $watermark_setting->regenerateUUID();
        if ($request->has("watermark_image")) {
            $watermark_setting->setImagePath();
        }
        try {
            ProductWatermarkSettingService::setRecord($watermark_setting);
        } catch (NotValidSettingRecordException $e) {
            SystemMessageService::addErrorMessage("messages.product_watermark.update.failed");
            return History::redirectBack();
        }
        SystemMessageService::addSuccessMessage("messages.product_watermark.update.success");
        return History::redirectBack();
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(product_id="exists:products,id")
     */
    public function process(Request $request) {
        $watermark_setting = ProductWatermarkSettingService::getRecord();

        if ($request->has("product_id")) {
            /** @var Product $product */
            $product = Product::with(["images"])->find($request->get("product_id"));
            $apply_product_watermark = new ApplyProductWatermarkJob($product, $watermark_setting);
            $this->dispatch($apply_product_watermark);
        } else {
            Product::with(["images"])->chunk(100,
                /**
                 * @param Collection|Product[] $products
                 */
                function (array|Collection $products) use ($watermark_setting) {
                    foreach ($products as $product) {
                        $apply_product_watermark = new ApplyProductWatermarkJob($product, $watermark_setting);
                        $this->dispatch($apply_product_watermark);
                    }
                });
        }

        SystemMessageService::addSuccessMessage("messages.product_watermark.process.success");
        return History::redirectBack();
    }

    public function removeImage(): \Illuminate\Http\RedirectResponse {
        $watermark_setting = ProductWatermarkSettingService::getRecord();
        $watermark_setting->setWatermarkImage("");
        try {
            ProductWatermarkSettingService::setRecord($watermark_setting);
        } catch (NotValidSettingRecordException $e) {
            SystemMessageService::addErrorMessage("messages.product_watermark.remove_image.failed");
            return History::redirectBack();
        }
        SystemMessageService::addSuccessMessage("messages.product_watermark.remove_image.success");
        return History::redirectBack();
    }

    public function getModel(): ?string {
        return null;
    }
}
