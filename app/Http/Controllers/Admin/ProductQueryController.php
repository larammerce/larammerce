<?php

namespace App\Http\Controllers\Admin;


use App\Models\ProductQuery;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class ProductQueryController extends BaseController
{

    /**
     * @role(super_user, cms_manager)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $product_queries = ProductQuery::paginate(ProductQuery::getPaginationCount());
        return view('admin.pages.product-query.index', compact('product_queries'));
    }


    /**
     * @role(super_user, cms_manager)
     */
    public function create(): Factory|View|Application
    {
        return view('admin.pages.product-query.create');
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(title="required", identifier="required|unique:product_queries")
     */
    public function store(Request $request): RedirectResponse
    {
        $product_query = ProductQuery::create($request->all());
        return redirect()->route('admin.product-query.edit', $product_query);
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function show(ProductQuery $product_query): \Response|Response|Application|ResponseFactory
    {
        return response("there is no show page yet.");
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(ProductQuery $product_query): Factory|View|Application
    {
        return view('admin.pages.product-query.edit', compact("product_query"));
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function update(Request $request, ProductQuery $product_query): RedirectResponse
    {
        $data = $request->except(["identifier"]);
        $product_query->update($data);
        return History::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(ProductQuery $product_query): RedirectResponse
    {
        $product_query->delete();
        return History::redirectBack();
    }


    public function getModel(): ?string
    {
        return ProductQuery::class;
    }
}
