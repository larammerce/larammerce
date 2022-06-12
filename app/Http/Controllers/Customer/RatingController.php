<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/27/2017 AD
 * Time: 16:59
 */

namespace App\Http\Controllers\Customer;

use App\Models\Product;
use App\Utils\CMS\SystemMessageService;
use Illuminate\Http\Request;

class RatingController extends BaseController
{
    /**
     * @rules(value="required|numeric|min:1|max:5")
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function product(Request $request, Product $product)
    {
        if ($product->submitRating($request->get('value'), $request->get('comment')))
            SystemMessageService::addSuccessMessage("system_messages.rating.submitted");
        else
            SystemMessageService::addErrorMessage("system_messages.rating.failed");

        return redirect()->to($product->getFrontUrl());
    }
}