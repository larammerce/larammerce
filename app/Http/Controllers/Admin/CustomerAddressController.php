<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HistoryHelper;
use App\Models\CustomerAddress;
use App\Models\CustomerUser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class CustomerAddressController extends BaseController
{
    /**
     * @role(super_user, stock_manager, acc_manager)
     * @rules(customer_user_id="exists:customer_users,id")
     */
    public function index(): Factory|View|Application
    {
        $customer_user = null;
        if (request()->has('customer_user_id')) {
            parent::setPageAttribute(request()->get("customer_user_id"));
            $customer_user = CustomerUser::find(request()->get('customer_user_id'));
            $customer_addresses = $customer_user->addresses()->with('customer', 'state', 'city', 'district')
                ->paginate(CustomerAddress::getPaginationCount());
        } else {
            parent::setPageAttribute();
            $customer_addresses = CustomerAddress::with('customer', 'state', 'city', 'district')
                ->paginate(CustomerAddress::getPaginationCount());
        }
        return view('admin.pages.customer-address.index', compact('customer_addresses', 'customer_user'));
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     * @rules(customer_user_id="required|exists:customer_users,id")
     */
    public function create(): Factory|View|Application
    {
        $customer_user = CustomerUser::find(request()->get('customer_user_id'));
        return view('admin.pages.customer-address.create', compact('customer_user'));
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     * @rules(name="required", customer_user_id="required|exists:customer_users,id",
     *     state_id="required|exists:states,id", city_id="required|exists:cities,id",
     *     district_id="exists:districts,id", phone_number="required", zipcode="required", superscription="required",
     *     transferee_name="required")
     */
    public function store(Request $request): RedirectResponse
    {
        $customer_address = CustomerAddress::create($request->all());
        return redirect()->to(route('admin.customer-address.index') . '?customer_user_id=' . $customer_address->customer_user_id);
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     */
    public function show(CustomerAddress $customer_address)
    {
        //TODO : we must make a view page for customer_addresses
        return response()->make($customer_address->superscription);
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     */
    public function edit(CustomerAddress $customer_address): Factory|View|Application
    {
        $customer_address->load('customer', 'state', 'city', 'district');
        return view('admin.pages.customer-address.edit')->with(compact("customer_address"));
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     * @rules(name="required", state_id="required|exists:states,id", city_id="required|exists:cities,id",
     *     district_id="exists:districts,id", phone_number="required", zipcode="required",
     *     superscription="required", transferee_name="required")
     */
    public function update(Request $request, CustomerAddress $customer_address): RedirectResponse
    {
        $customer_address->update($request->all());
        return HistoryHelper::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(CustomerAddress $customer_address): RedirectResponse
    {
        $customer_address->delete();
        return back();
    }


    public function getModel(): ?string
    {
        return CustomerAddress::class;
    }
}
