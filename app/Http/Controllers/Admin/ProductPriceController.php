<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HistoryHelper;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class ProductPriceController extends BaseController
{
    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(product_id="required|exists:products,id")
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute(request()->get("product_id"));
        $product = Product::find(request()->get('product_id'));
        $product_prices = $product->prices()->paginate(ProductPrice::getPaginationCount());
        return view('admin.pages.product-price.index', compact('product_prices', 'product'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(product_id="required|exists:products,id")
     */
    public function create(Request $request): Factory|View|Application
    {
        $product = Product::find($request->get('product_id'));
        return view('admin.pages.product-price.create', compact('product'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(product_id="required|integer|exists:products,id",value="required|integer")
     */
    public function store(Request $request): RedirectResponse
    {
        ProductPrice::create($request->all());
        $product = Product::find($request->get('product_id'));
        $product->update([
            "latest_price" => $request->get("value")
        ]);
        return redirect()->route('admin.product.edit', $product);
    }

    /**
     * @role(super_user)
     */
    public function destroy(ProductPrice $product_price): RedirectResponse
    {
        $product_price->delete();
        return HistoryHelper::redirectBack();
    }


    public function getModel(): ?string
    {
        return ProductPrice::class;
    }
}
