<?php
/**
 */

namespace App\Http\Controllers\Admin;


use App\Helpers\HistoryHelper;
use App\Models\ProductFilter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class ProductFilterController extends BaseController
{

    /**
     * @role(super_user, cms_manager)
     */
    public function index(): Response
    {
        parent::setPageAttribute();
        $product_filters = ProductFilter::paginate(ProductFilter::getPaginationCount());
        return response()->view('admin.pages.product-filter.index', compact('product_filters'));
    }


    /**
     * @role(super_user, cms_manager)
     */
    public function create(): Response
    {
        return response()->view('admin.pages.product-filter.create');
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(title="required", identifier="required|unique:product_filters",
     *      product_query_id="exists:product_queries,id")
     */
    public function store(Request $request): Response
    {
        $data = $request->all();
        $product_filter = ProductFilter::create($data);
        return redirect()->route('admin.product-filter.edit', $product_filter);
    }

    /**
     * @role(super_user)
     */
    public function show(ProductFilter $product_filter): Response
    {
        return response()->make("there is no show page yet.");
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(ProductFilter $product_filter): Response
    {
        return response()->view('admin.pages.product-filter.edit', ["product_filter" => $product_filter]);
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(title="required", product_query_id="exists:product_queries,id")
     */
    public function update(Request $request, ProductFilter $product_filter): Response
    {
        $data = $request->except(["identifier"]);
        $product_filter->update($data);
        return HistoryHelper::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(ProductFilter $product_filter): Response
    {
        $product_filter->delete();
        return HistoryHelper::redirectBack();
    }


    public function getModel(): ?string
    {
        return ProductFilter::class;
    }
}
