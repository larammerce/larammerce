<?php
/**
 */

namespace App\Http\Controllers\Admin;


use App\Models\CustomerUser;
use App\Models\NeedList;
use App\Models\Product;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class NeedListController extends BaseController
{

    /**
     * @role(super_user, acc_manager, stock_manager, cms_manager)
     */
    public function showProducts(): Factory|View|Application
    {
        parent::setPageAttribute();
        $need_lists = NeedList::with(["product", "customer"])->paginate(NeedList::getPaginationCount());
        return view('admin.pages.need-list.product.index', compact('need_lists'));
    }

    /**
     * @role(super_user, acc_manager, stock_manager, cms_manager)
     */
    public function showProduct(Request $request, Product $product): Factory|View|Application
    {
        parent::setPageAttribute("customers_p_{$product->id}");
        $customer_users = $product->needLists()->withPivot("created_at")
            ->paginate(CustomerUser::getPaginationCount());
        return view('admin.pages.need-list.customer-user.index', compact('product', 'customer_users'));
    }

    public function getModel(): ?string
    {
        return NeedList::class;
    }
}
