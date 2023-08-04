<?php
/**
 */

namespace App\Http\Controllers\Admin;

use App\Models\DiscountGroup;
use App\Models\ProductFilter;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class DiscountGroupController extends BaseController
{

    /**
     * @role(super_user, acc_manager)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $discount_groups = DiscountGroup::with('cards')->paginate(DiscountGroup::getPaginationCount());
        return view('admin.pages.discount-group.index', compact("discount_groups"));
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function create(): Factory|View|Application
    {
        return view('admin.pages.discount-group.create');
    }

    /**
     * @role(super_user, acc_manager)
     * @rules(title="required")
     */
    public function store(Request $request): RedirectResponse
    {
        $discount_group = DiscountGroup::create($request->all());
        return redirect()->route('admin.discount-group.edit', $discount_group);
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function show(DiscountGroup $discount_group): RedirectResponse
    {
        return redirect()->to(route('admin.discount-card.index') .
            "?discount_group_id={$discount_group->id}");
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function edit(DiscountGroup $discount_group)
    {
        return view('admin.pages.discount-group.edit', compact("discount_group"));
    }

    /**
     * @role(super_user, acc_manager)
     * @rules(title="required", is_assigned="required", is_percentage="required", expiration_date="date",
     *     value="required", is_multi="required", is_event="required",
     *     steps_data.*.amount="numeric", steps_data.*.value="numeric")
     */
    public function update(Request $request, DiscountGroup $discount_group)
    {
        $discount_group->update($request->all());
        return History::redirectBack();
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function destroy(DiscountGroup $discount_group)
    {
        $discount_group->is_active = !($discount_group->is_active);
        $discount_group->save();
        return History::redirectBack();
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function softDelete(DiscountGroup $discount_group)
    {
        $discount_group->delete();
        return redirect()->back();
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function indexProductFilter(DiscountGroup $discount_group): Response
    {
        parent::setPageAttribute($discount_group->id);
        $product_filters = $discount_group->filters()->paginate(ProductFilter::getPaginationCount());
        return response()->view("admin.pages.discount-group.product-filter.index", compact("discount_group", "product_filters"));
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function createProductFilter(DiscountGroup $discount_group): Response
    {
        $product_filters = ProductFilter::all();
        return response()->view("admin.pages.discount-group.product-filter.create", compact("discount_group", "product_filters"));
    }

    /**
     * @role(super_user, acc_manager)
     * @rules(product_filter_id="required|exists:product_filters,id")
     */
    public function attachProductFilter(DiscountGroup $discount_group): RedirectResponse
    {
        $product_filter = ProductFilter::find(request()->get("product_filter_id"));
        $product_filter->attachToDiscountGroup($discount_group);
        return redirect()->route("admin.discount-group.product-filter.index", $discount_group);
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function detachProductFilter(DiscountGroup $discount_group, ProductFilter $product_filter): RedirectResponse
    {
        $product_filter->detachFromDiscountGroup($discount_group);
        return redirect()->route("admin.discount-group.product-filter.index", $discount_group);
    }

    public function getModel(): ?string
    {
        return DiscountGroup::class;
    }
}
