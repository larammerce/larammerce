<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HistoryHelper;
use App\Helpers\RequestHelper;
use App\Helpers\SystemMessageHelper;
use App\Models\CustomerUser;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class CustomerUserController extends BaseController
{
    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $customer_users = CustomerUser::with('user', 'addresses', 'invoices')
            ->paginate(CustomerUser::getPaginationCount());
        return view('admin.pages.customer-user.index', compact('customer_users'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(id="required|exists:users,id")
     */
    public function create(): Factory|View|Application
    {
        $user = User::find(request()->get('id'));
        return view('admin.pages.customer-user.create', compact('user'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(user_id="required|exists:users,id", main_phone="required|unique:customer_users",
     *     is_legal_person="boolean", national_code="required|national_code", credit="numeric",
     *     bank_account_card_number="nullable|min:16|max:16", bank_account_uuid="nullable|min:24|max:24")
     */
    public function store(Request $request): RedirectResponse|Response
    {
        RequestHelper::setAttr('is_initiated', true);
        RequestHelper::setAttr('is_active', false);
        $customer = CustomerUser::create($request->all());
        $customer->user->update(['is_customer_user' => true]);

        if ($customer->user->saveFinManCustomer())
            $customer->update(['is_active' => true]);
        else
            SystemMessageHelper::addErrorMessage("messages.customer_user.activation_failed");

        return redirect()->route('admin.customer-user.edit');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function show(CustomerUser $customer_user)
    {
        //TODO : we must make a view page for customer_users
        return response()->make('customer user show page');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function edit(CustomerUser $customer_user): Factory|View|Application
    {
        $customer_user->load('user', 'addresses', 'invoices');
        return view('admin.pages.customer-user.edit')->with(compact("customer_user"));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(main_phone="required", is_legal_person="boolean", credit="numeric",
     *     bank_account_card_number="nullable|min:16|max:16", bank_account_uuid="nullable|min:24|max:24")
     */
    public function update(Request $request, CustomerUser $customer_user): RedirectResponse
    {
        $customer_user->update($request->all());
        $customer_user->user->updateFinManCustomer();
        return HistoryHelper::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(CustomerUser $customer_user): RedirectResponse
    {
        $customer_user->delete();
        return back();
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function activate(Request $request, CustomerUser $customer_user): RedirectResponse
    {
        if ($customer_user->user->saveFinManCustomer())
            $customer_user->update(['is_active' => true]);
        else
            SystemMessageHelper::addErrorMessage("messages.customer_user.activation_failed");
        return HistoryHelper::redirectBack();
    }


    public function getModel(): ?string
    {
        return CustomerUser::class;
    }
}
