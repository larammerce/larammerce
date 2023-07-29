<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\Product\ProductPackageItemInvalidCountException;
use App\Exceptions\Product\ProductPackageItemInvalidIdException;
use App\Exceptions\Product\ProductPackageItemNotFoundException;
use App\Exceptions\Product\ProductPackageNotExistsException;
use App\Helpers\HistoryHelper;
use App\Helpers\RequestHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\SystemMessageHelper;
use App\Models\Product;
use App\Models\ProductPackage;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class ProductPackageController extends BaseController
{

    /**
     * @role(super_user, acc_manager)
     */
    public function edit(Product $product): Factory|View|Application
    {
        $product->load('productPackage');
        return view("admin.pages.product-package.edit")->with(["product" => $product]);
    }

    /**
     * @rules(product_items="array",
     *     product_items.*.product_id="numeric|exists:products,id",
     *     product_items.*.product_count="numeric")
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        try {
            if ($product->is_package and $request->has('product_items'))
                $product->syncPackageItems($request->get('product_items'));
            return HistoryHelper::redirectBack();
        } catch (ProductPackageNotExistsException $exception) {
            SystemMessageHelper::addErrorMessage('system_messages.product_package.not_exists');
            return redirect()->back()->withInput();
        } catch (ProductPackageItemNotFoundException $exception) {
            SystemMessageHelper::addErrorMessage('system_messages.product_package.item_not_found');
            return redirect()->back()->withInput();
        } catch (ProductPackageItemInvalidIdException $exception) {
            SystemMessageHelper::addErrorMessage('system_messages.product_package.item_invalid_id');
            return redirect()->back()->withInput();
        } catch (ProductPackageItemInvalidCountException $exception) {
            SystemMessageHelper::addErrorMessage('system_messages.product_package.item_invalid_count');
            return redirect()->back()->withInput();
        }
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(id="required|exists:products,id")
     */
    public function attachProduct(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        if (!$product->is_package) {
            $product->productPackages()->attach($request->get('id'));
            if (RequestHelper::isRequestAjax()) {
                return response()->json(ResponseHelper::create(
                    ['messages.product_package.product_attached'], 200, compact('product')
                ), 200);
            }
        } else {
            if (RequestHelper::isRequestAjax()) {
                return response()->json(ResponseHelper::create(
                    ['messages.product_package.product_not_attached'], 400, compact('product')
                ), 400);
            }
        }
        return redirect()->route('admin.product.edit', $product);
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(id="required|exists:products,id")
     */
    public function detachProduct(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        if (!$product->is_package) {
            $product->productPackages()->detach($request->get('id'));
            if (RequestHelper::isRequestAjax()) {
                return response()->json(ResponseHelper::create(
                    ['messages.product_package.product_detached'], 200, compact('product')
                ), 200);
            }
        } else {
            if (RequestHelper::isRequestAjax()) {
                return response()->json(ResponseHelper::create(
                    ['messages.product_package.product_not_detached'], 400, compact('product')
                ), 400);
            }
        }
        return redirect()->route('admin.product.edit', $product);
    }

    /**
     * @role(super_user)
     */
    public function destroy(ProductPackage $product_package): RedirectResponse
    {
        $product_package->delete();
        return back();
    }


    public function getModel(): ?string
    {
        return ProductPackage::class;
    }
}
