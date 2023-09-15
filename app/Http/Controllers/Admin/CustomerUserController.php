<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\Utils\Common\History;
use Illuminate\Contracts\View\View;
use App\Utils\Common\RequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use App\Utils\CMS\SystemMessageService;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Foundation\Application;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class CustomerUserController extends BaseController
{
    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function index(Request $request): Factory|View|Application {

        $scope = $request->has('follow') ? 'follow' : 'active';
        parent::setPageAttribute($scope);

        $customers_query = CustomerUser::query()->with('user', 'addresses', 'invoices');

        if($request->has('follow')){
            if(request('follow')=='1'){
                $customer_users = $customers_query->whereHas('invoices', function (Builder $query){
                    $query->where('sum', '<', 10000);
                })->paginate(CustomerUser::getPaginationCount());
            }elseif(request('follow')=='2'){
                $customer_users = $customers_query->whereHas('invoices', function (Builder $query){
                    $query->where('sum', '<', 2000000);
                })->paginate(CustomerUser::getPaginationCount());
            }    
        }else{
            $customer_users = $customers_query->paginate(CustomerUser::getPaginationCount());
        }
        
        return view('admin.pages.customer-user.index', compact('customer_users', 'scope'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(id="required|exists:users,id")
     */
    public function create(): Application|Factory|View|RedirectResponse {
        $user = User::find(request()->get('id'));
        if ($user->customerUser !== null)
            return redirect()->route('admin.user.edit', $user->id);
        return view('admin.pages.customer-user.create', compact('user'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(user_id="required|exists:users,id", main_phone="required|unique:customer_users",
     *     is_legal_person="boolean", national_code="nullable|national_code", credit="numeric",
     *     bank_account_card_number="nullable|min:16|max:16", bank_account_uuid="nullable|min:24|max:24")
     */
    public function store(Request $request): RedirectResponse|Response {
        RequestService::setAttr('is_initiated', true);
        RequestService::setAttr('is_active', false);
        $customer = CustomerUser::create($request->all());
        $customer->user->update(['is_customer_user' => true]);

        if ($customer->user->saveFinManCustomer())
            $customer->update(['is_active' => true]);
        else
            SystemMessageService::addErrorMessage("messages.customer_user.activation_failed");

        return redirect()->route('admin.customer-user.edit', $customer->id);
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function show(CustomerUser $customer_user) {
        //TODO : we must make a view page for customer_users
        return response()->make('customer user show page');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function edit(CustomerUser $customer_user): Factory|View|Application {
        $customer_user->load('user', 'addresses', 'invoices');
        return view('admin.pages.customer-user.edit')->with(compact("customer_user"));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(main_phone="required", is_legal_person="boolean", credit="numeric",
     *     bank_account_card_number="nullable|min:16|max:16", bank_account_uuid="nullable|min:24|max:24")
     */
    public function update(Request $request, CustomerUser $customer_user): RedirectResponse {
        $customer_user->update($request->all());
        $customer_user->user->updateFinManCustomer();
        return History::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(CustomerUser $customer_user): RedirectResponse {
        $customer_user->delete();
        return back();
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function activate(Request $request, CustomerUser $customer_user): RedirectResponse {
        if ($customer_user->user->saveFinManCustomer())
            $customer_user->update(['is_active' => true]);
        else
            SystemMessageService::addErrorMessage("messages.customer_user.activation_failed");
        return History::redirectBack();
    }


    public function getModel(): ?string {
        return CustomerUser::class;
    }
}
