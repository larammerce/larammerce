<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HistoryHelper;
use App\Helpers\ImageHelper;
use App\Helpers\RequestHelper;
use App\Helpers\ResponseHelper;
use App\Models\ProductImage;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class ProductImageController extends BaseController
{

    /**
     * @role(super_user, cms_manager)
     * @rules(product_id="required|exists:products,id",
     *     image="required|image|max:2048|dimensions:min_width=".get_image_min_width('product').",ratio=".get_image_ratio('product'))
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $tmp_image = ImageHelper::saveImage('product');
            $model = new ProductImage();
            $model->real_name = $tmp_image->name;
            $model->path = $tmp_image->destinationPath;
            $model->extension = $tmp_image->extension;
            $model->product_id = $request->get('product_id');
            $model->save();

            if (RequestHelper::isRequestAjax())
                return response()->json(ResponseHelper::create(
                    ['messages.product_image.image_uploaded'], 200, compact('model')
                ), 200);

            return redirect()->route('admin.pages.product-image.index');
        } catch (Exception $e) {
            return response()->json(ResponseHelper::create(
                ['messages.product_image.image_not_uploaded', $e->getMessage()], 500
            ), 500);
        }
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function update(Request $request, ProductImage $product_image): JsonResponse|RedirectResponse
    {
        $product_image->update($request->all());
        if (RequestHelper::isRequestAjax())
            return response()->json(ResponseHelper::create(
                ['messages.product_image.image_edited'], 200
            ), 200);
        return HistoryHelper::redirectBack();
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function destroy(ProductImage $product_image): JsonResponse|RedirectResponse
    {
        $product_image->delete();
        if (RequestHelper::isRequestAjax())
            return response()->json(ResponseHelper::create(
                ['messages.product_image.image_deleted'], 200
            ), 200);
        return back();
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function setAsMainImage(ProductImage $product_image): JsonResponse|RedirectResponse
    {
        $product_image->product->images()->update(["is_main" => false]);
        $product_image->is_main = true;
        $product_image->save();
        if (RequestHelper::isRequestAjax())
            return response()->json(ResponseHelper::create(
                ['messages.product_image.main_image_changed'], 200
            ), 200);
        return redirect()->route('admin.product.edit', $product_image->product);
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function setAsSecondaryImage(ProductImage $product_image): JsonResponse|RedirectResponse
    {
        $product_image->product->images()->update(["is_secondary" => false]);
        $product_image->is_secondary = true;
        $product_image->save();
        if (RequestHelper::isRequestAjax())
            return response()->json(ResponseHelper::create(
                ['messages.product_image.secondary_image_changed'], 200
            ), 200);
        return redirect()->route('admin.product.edit', $product_image->product);
    }


    public function getModel(): ?string
    {
        return ProductImage::class;
    }
}
