<?php
/**
 */

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\DiscountGroup;
use App\Models\ProductFilter;
use App\Utils\Common\History;
use Illuminate\Http\Response;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;
use App\Services\DiscountGroup\DiscountGroupService;
use App\Utils\CMS\SystemMessageService;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class DiscountGroupController extends BaseController
{

    /**
     * @role(super_user, acc_manager)
     */
    public function index(Request $request): Factory|View|Application
    {
        $show_deleted = $this->mustShowDeletedItems($request);
        $scope = $show_deleted ? "deleted" : "active";
        parent::setPageAttribute($scope);
        $discount_groups = $show_deleted ?
            DiscountGroupService::getDeletedPaginated() :
            DiscountGroupService::getAllPaginated();
        return view('admin.pages.discount-group.index', compact('discount_groups', 'show_deleted', 'scope'));
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
        try{
            $discount_group->delete();
            SystemMessageService::addSuccessMessage("messages.discount_group.soft_delete_success");
        }catch(Exception $e){
            SystemMessageService::addErrorMessage("messages.discount_group.soft_delete_fail");
        }
        return redirect()->back();
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function restore(Request $request, int $discount_group_id)
    {
        try{
            $discount_group = DiscountGroup::onlyTrashed()->find($discount_group_id);
            $discount_group->restore();
            SystemMessageService::addSuccessMessage("messages.discount_group.restore_success");
        }catch(Exception $e){
            SystemMessageService::addErrorMessage("messages.discount_group.restore_fail");
        }
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

    private function mustShowDeletedItems(Request $request)
    {
        return $request->has("deleted") ? true : false;
    }
}
