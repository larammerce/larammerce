<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/23/2017 AD
 * Time: 19:05
 */

namespace App\Http\Controllers\Customer;


use App\Models\CustomerUser;
use App\Models\Product;
use App\Utils\CMS\SystemMessageService;

class WishListController extends BaseController
{
    public function index()
    {
        return h_view('public.wish-list',
            [
                "wishList" => get_customer_user()->wishList
            ]);
    }

    public function attachProduct(Product $product)
    {
        $customer = get_customer_user();
        $customer->wishList()->syncWithoutDetaching([$product->id]);
        SystemMessageService::addSuccessMessage("system_messages.wish_list.attached");
        return redirect()->to($product->getFrontUrl());
    }

    public function detachProduct(Product $product)
    {
        $customer = get_customer_user();
        $customer->wishList()->detach($product->id);
        SystemMessageService::addSuccessMessage("system_messages.wish_list.detached");
        return redirect()->back();
    }
}