<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductSpecialPrice;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class ProductSpecialPriceController extends BaseController
{
    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(product_id="required|exists:products,id")
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute(request()->get("product_id"));
        $product = Product::find(request()->get('product_id'));
        $product_special_prices = $product->specialPrices()->paginate(ProductSpecialPrice::getPaginationCount());
        return view('admin.pages.product-special-price.index', compact('product_special_prices', 'product'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(product_id="required|exists:products,id")
     */
    public function create(Request $request): Factory|View|Application
    {
        $product = Product::find($request->get('product_id'));
        return view('admin.pages.product-special-price.create', compact('product'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(product_id="required|integer|exists:products,id",value="required|integer")
     */
    public function store(Request $request): RedirectResponse
    {
        $product = Product::find($request->get('product_id'));
        $product->update([
            "latest_special_price" => $request->get("value")
        ]);
        return redirect()->route('admin.product.edit', $product);
    }

    /**
     * @role(super_user)
     */
    public function destroy(ProductSpecialPrice $product_special_price): RedirectResponse
    {
        try {
            $product_special_price->delete();
        } catch (Exception $e) {
            die($e->getMessage());
        }
        return back();
    }

    public function getModel(): ?string
    {
        return ProductSpecialPrice::class;
    }
}
