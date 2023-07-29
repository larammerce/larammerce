<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/23/2017 AD
 * Time: 19:05
 */

namespace App\Http\Controllers\Customer;

use App\Helpers\SystemMessageHelper;
use App\Models\Product;

class NeedListController extends BaseController
{
    public function attachProduct(Product $product)
    {
        if (!$product->is_active) {
            $customer = get_customer_user();
            $customer->needList()->syncWithoutDetaching([$product->id]);
            SystemMessageHelper::addSuccessMessage("system_messages.need_list.attached");
        }
        return redirect()->to($product->getFrontUrl());
    }

    public function detachProduct(Product $product)
    {
        $customer = get_customer_user();
        $customer->needList()->detach($product->id);
        SystemMessageHelper::addSuccessMessage("system_messages.need_list.detached");
        return redirect()->back();
    }
}