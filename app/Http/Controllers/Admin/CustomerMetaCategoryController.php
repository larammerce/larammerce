<?php


namespace App\Http\Controllers\Admin;


use App\Helpers\HistoryHelper;
use App\Models\CustomerMetaCategory;
use App\Models\Directory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class CustomerMetaCategoryController extends BaseController
{

    /**
     * @role(super_user, acc_manager)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $customer_meta_categories = CustomerMetaCategory::main()->with("items")
            ->paginate(CustomerMetaCategory::getPaginationCount());
        return view("admin.pages.customer-meta-category.index", compact("customer_meta_categories"));
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function create(): Factory|View|Application
    {
        return view("admin.pages.customer-meta-category.create");
    }

    /**
     * @role(super_user, acc_manager)
     * @rules(title="required", needs_admin_confirmation="required|boolean", form_blade_name="required")
     */
    public function store(Request $request): RedirectResponse
    {
        $customer_meta_category = CustomerMetaCategory::create($request->all());
        return redirect()->route("admin.customer-meta-category.edit", $customer_meta_category);
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function show(CustomerMetaCategory $customer_meta_category)
    {

    }

    /**
     * @role(super_user, acc_manager)
     */
    public function edit(CustomerMetaCategory $customer_meta_category): Factory|View|Application
    {
        return view("admin.pages.customer-meta-category.edit", compact("customer_meta_category"));
    }

    /**
     * @role(super_user, acc_manager)
     * @rules(title="required", needs_admin_confirmation="required|boolean", form_blade_name="required")
     */
    public function update(Request $request, CustomerMetaCategory $customer_meta_category): RedirectResponse
    {
        $customer_meta_category->data_object = array_values($request->get("data_object"));
        $customer_meta_category->update($request->all());
        return HistoryHelper::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(CustomerMetaCategory $customer_meta_category): RedirectResponse
    {
        $customer_meta_category->delete();
        return HistoryHelper::redirectBack();
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function clone(Request $request, Directory $directory): RedirectResponse
    {
        if (!$directory->hasCustomerMetaCategory())
            return HistoryHelper::redirectBack();
        $customer_meta_category = $directory->customerMetaCategory;
        $customer_meta_category->update(["parent_id" => $customer_meta_category->id]);
        $new_customer_meta_category = $customer_meta_category->replicate();
        $new_customer_meta_category->save();
        $directory->update(["cmc_id" => $new_customer_meta_category->id]);
        return redirect()->route("admin.customer-meta-category.edit", $new_customer_meta_category);
    }

    public function getModel(): ?string
    {
        return CustomerMetaCategory::class;
    }
}
