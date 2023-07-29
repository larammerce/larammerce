<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/27/2017 AD
 * Time: 16:59
 */

namespace App\Http\Controllers\Customer;

use App\Helpers\SystemMessageHelper;
use App\Models\Product;
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
            SystemMessageHelper::addSuccessMessage("system_messages.rating.submitted");
        else
            SystemMessageHelper::addErrorMessage("system_messages.rating.failed");

        return redirect()->to($product->getFrontUrl());
    }
}