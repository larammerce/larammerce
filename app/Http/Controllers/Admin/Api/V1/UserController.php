<?php
/**
 */

namespace App\Http\Controllers\Admin\Api\V1;

use App\Models\CustomerUser;
use App\Models\CustomerUserLegalInfo;
use App\Models\SystemUser;
use App\Models\User;
use App\Utils\Common\MessageFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin\Api\V1
 * @role(enabled=true)
 */
class UserController extends BaseController
{
    /**
     * @role(super_user, acc_manager, stock_manager, cms_manager)
     * @rules(query="required")
     */
    public function query(Request $request): JsonResponse
    {
        $query = $request->get('query');
        $collection = User::search($query)
            ->with('customerUser.legalInfo', 'systemUser');
        $customerUsers = User::whereIn('id',
            CustomerUser::search($query)->get()->merge(
                CustomerUser::whereIn('id',
                    CustomerUserLegalInfo::search($query)->pluck('customer_user_id')->toArray())->get()
            )->pluck('user_id')->toArray())->with('customerUser.legalInfo', 'systemUser')->get();
        $systemUsers = User::whereIn('id', SystemUser::search($query)->pluck('user_id')->toArray()
        )->with('customerUser.legalInfo', 'systemUser')->get();

        if ($request->has('only_customer_users')) {
            $collection = $collection->customerUsers()->get()->merge($customerUsers);
        } else if ($request->has('only_system_users')) {
            $collection = $collection->systemUsers()->get()->merge($systemUsers);
        } else {
            $collection = $collection->get()->merge($customerUsers)->merge($systemUsers);
        }

        return response()->json(MessageFactory::create(
            [], 200, compact('collection')
        ), 200);
    }

    public function getModel(): ?string
    {
        return User::class;
    }
}
