<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Comparison\ComparisonService;
use App\Utils\Comparison\InitialProductNotFoundException;
use App\Utils\Comparison\ProductStructureMismatchException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ComparisonController extends Controller
{

    public function show(): Factory|Application|View
    {
        $product = ComparisonService::getInitialProduct();

        if ($product === null) {
            SystemMessageService::addWarningMessage("system_messages.comparison.initial_product_not_found");
            abort(404);
        }

        return h_view('public.product-compare', [
            'product' => $product,
            'comparing_products' => ComparisonService::getComparingProducts()
        ]);
    }

    public function init(Product $product): RedirectResponse
    {
        ComparisonService::initProduct($product);
        return redirect()->route("comparison.show");
    }

    public function add(Product $product): RedirectResponse
    {
        try {
            ComparisonService::addProduct($product);
        } catch (InitialProductNotFoundException $e) {
            SystemMessageService::addErrorMessage("system_messages.comparison.initial_product_not_found");
        } catch (ProductStructureMismatchException $e) {
            SystemMessageService::addErrorMessage("system_messages.comparison.product_structure_mismatch");
        }
        return redirect()->route("comparison.show");
    }

    public function remove(Product $product): RedirectResponse
    {
        try {
            ComparisonService::removeProduct($product);
        } catch (InitialProductNotFoundException $e) {
            SystemMessageService::addErrorMessage("system_messages.comparison.initial_product_not_found");
        } catch (ProductStructureMismatchException $e) {
            SystemMessageService::addErrorMessage("system_messages.comparison.product_structure_mismatch");
        }
        return redirect()->route("comparison.show");
    }
}
