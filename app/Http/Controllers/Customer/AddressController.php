<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/28/2017 AD
 * Time: 12:44
 */

namespace App\Http\Controllers\Customer;


use App\Models\CustomerAddress;
use App\Services\Customer\CustomerAddressService;
use App\Utils\CMS\Setting\CustomerLocation\CustomerLocationModel;
use App\Utils\CMS\Setting\CustomerLocation\CustomerLocationService;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\History;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddressController extends BaseController
{
    private CustomerAddressService $customer_address_service;

    public function __construct(CustomerAddressService $customer_address_service) {
        parent::__construct();
        $this->customer_address_service = $customer_address_service;
    }

    public function create() {
        return h_view('public.address-add');
    }

    /**
     * @rules(state_id="required|exists:states,id", city_id="required|exists:cities,id",
     *     zipcode="nullable|regex:/[0-9]{10}/|min:10|max:10", phone_number="required",
     *     superscription="required", transferee_name="required")
     * @param Request $request
     * @description(comment="this method is for adding an address to user")
     * @return RedirectResponse
     */
    public function store(Request $request) {
        $customerUser = get_customer_user("web");
        $address = new CustomerAddress($request->all());
        $address->customer_user_id = $customerUser->id;
        $address->name = trans('ecommerce.user.address') . ' ' . (count($customerUser->addresses) + 1);
        $address->save();
        $this->customer_address_service->setAddressAsMain($address);

        $defaultResponse = redirect()->to('/');
        if ($customerUser->cartRows()->get()->count() > 0) {
            $defaultResponse = redirect()->route('customer.cart.show');
        }

        return History::redirectBack($defaultResponse);
    }

    /**
     * @param CustomerAddress $customer_address
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerAddress $customer_address) {
        if ($customer_address->customer_user_id != get_customer_user("web")->id)
            abort(403);

        $customer_address->load('state', 'city', 'district');
        return h_view('public.address-edit', compact('customer_address'));
    }

    /**
     * @rules(district_id="exists:districts,id", phone_number="required",
     *     superscription="required", transferee_name="required")
     * @param Request $request
     * @param CustomerAddress $customer_address
     * @return RedirectResponse|Response
     */
    public function update(Request $request, CustomerAddress $customer_address) {
        //TODO: not safe, any user can edit others addresses.
        $customer_address->update($request->all());

        return History::redirectBack(redirect()->route('customer.profile.index'));
    }

    /**
     * @param CustomerAddress $address
     * @return RedirectResponse
     * @throws Exception
     */
    public function delete(CustomerAddress $address) {
        $customer = get_customer_user("web");
        if ($address->customer_user_id != $customer->id)
            abort(403);

        $isMain = $address->is_main;
        $address->delete();
        if ($isMain) {
            if (count(is_countable($customer->addresses) ? $customer->addresses : []) > 0) {
                $firstAddress = $customer->addresses()->first();
                $firstAddress->is_main = true;
                $firstAddress->save();
            }
        }
        return back();
    }

    /**
     * @rules(address_id="required|exists:customer_addresses,id")
     */
    public function setMain(): RedirectResponse {
        $address = CustomerAddress::find(request()->get("address_id"));
        $this->customer_address_service->setAddressAsMain($address);
        CustomerLocationService::setRecord(new CustomerLocationModel($address->state, $address->city));
        SystemMessageService::addSuccessMessage("system_messages.user.location_updated");
        return redirect()->back();
    }
}
