<?php
/**
 */

namespace App\Http\Controllers\Admin;

use App\Models\Coupon;
use App\Models\CustomerUser;
use App\Models\ProductFilter;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class CouponController extends BaseController
{

    /**
     * @role(super_user, acc_manager)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $coupons = Coupon::with('customer.user')->paginate(Coupon::getPaginationCount());
        return view('admin.pages.coupon.index', compact("coupons"));
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function create(): Factory|View|Application
    {
        return view('admin.pages.coupon.create');
    }

    /**
     * @role(super_user, acc_manager)
     * @rules(title="required", expire_at="date", amount="required|integer", phone_number="required|exists:customer_users,main_phone")
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var CustomerUser $customer_user */
        $customer_user = CustomerUser::where("main_phone", $request->get("phone_number"))->first();
        $request->merge([
            "customer_user_id" => $customer_user->id
        ]);
        $coupon = Coupon::create($request->all());
        SystemMessageService::addSuccessMessage("messages.coupon.created_successfully");
        return redirect()->route('admin.coupon.index');
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function show(Coupon $coupon)
    {
        return "Not implemented yet.";
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function edit(Coupon $coupon)
    {
        return view('admin.pages.coupon.edit', compact("coupon"));
    }

    /**
     * @role(super_user, acc_manager)
     * @rules(title="required")
     */
    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($request->all());
        SystemMessageService::addSuccessMessage("messages.coupon.updated_successfully");
        return History::redirectBack();
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return History::redirectBack();
    }

    public function getModel(): ?string
    {
        return Coupon::class;
    }
}
