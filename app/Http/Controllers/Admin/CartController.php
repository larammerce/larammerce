<?php

namespace App\Http\Controllers\Admin;

use App\Models\CartRow;
use App\Models\CustomerUser;
use Illuminate\Support\Facades\DB;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class CartController extends BaseController
{
    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function index()
    {
        parent::setPageAttribute();
        $cart_owners = CustomerUser::whereRaw(
            DB::raw("exists (select customer_carts.id from customer_carts where customer_carts.customer_user_id = customer_users.id)"))
            ->orderBy("is_cart_checked", "ASC")->orderBy("updated_at", "DESC")
            ->with(["user", "cartRows"])->paginate(CustomerUser::getPaginationCount());
        return view("admin.pages.cart.index", compact("cart_owners"));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function show(CustomerUser $customer_user)
    {
        return view("admin.pages.cart.show", compact("customer_user"));
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function setChecked(CustomerUser $customer_user)
    {
        $customer_user->update(["is_cart_checked" => true]);
        return redirect()->route("admin.cart.index");
    }

    public function getModel(): ?string
    {
        return CartRow::class;
    }
}
