<?php


namespace App\Http\Controllers\Customer;


use App\Models\CartRow;
use App\Models\CustomerMetaCategory;
use App\Models\CustomerMetaItem;
use Illuminate\Http\Request;

class MetaItemController extends BaseController
{
    public function create(Request $request, CartRow $cart_row, CustomerMetaCategory $customer_meta_category)
    {
        return h_view("public." . $customer_meta_category->form_blade_name,
            compact("customer_meta_category", "cart_row"));
    }

    public function store(Request $request, CartRow $cart_row, CustomerMetaCategory $customer_meta_category)
    {
        $cmi = $customer_meta_category->items()->create([
            "customer_user_id" => get_customer_user()->id,
            "data" => json_encode($request->all())
        ]);
        $cart_row->cmi_id = $cmi->id;
        $cart_row->save();
        return redirect()->route("customer.cart.show");
    }

    public function destroy(CustomerMetaItem $customer_meta_item)
    {
        if ($customer_meta_item->customer_user_id === get_customer_user()->id)
            $customer_meta_item->delete();
        return redirect()->route("customer.cart.show");
    }
}