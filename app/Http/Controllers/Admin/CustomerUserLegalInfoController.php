<?php

namespace App\Http\Controllers\Admin;

use App\Models\CustomerUserLegalInfo;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class CustomerUserLegalInfoController extends BaseController
{
    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $customer_users_legal_info = CustomerUserLegalInfo::with('customerUser.user', 'state', 'city')
            ->paginate(CustomerUserLegalInfo::getPaginationCount());
        return view('admin.pages.customer-user-legal-info.index', compact('customer_users_legal_info'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function create(): Factory|View|Application
    {
        return view('admin.pages.customer-user-legal-info.create');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(user_id="required|exists:users,id", mainPhone="required", is_legal_person="boolean",
     *     company_name="required_with:is_legal_person", company_code="required_with:is_legal_person")
     */
    public function store(Request $request): RedirectResponse
    {
        CustomerUserLegalInfo::create($request->all());
        return redirect()->route('admin.customer-user-legal-info.index');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function show(CustomerUserLegalInfo $customer_user_legal_info): Response|Application|ResponseFactory
    {
        return response('customer user legal info show page');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function edit(CustomerUserLegalInfo $customer_user_legal_info): Factory|View|Application
    {
        $customer_user_legal_info->load('customerUser.user', 'state', 'city');
        return view('admin.pages.customer-user-legal-info.edit')
            ->with(compact("customer_user_legal_info"));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(company_name="required", national_id="required",
     *     registration_code="required",company_phone="required", state_id="required|exists:states,id",
     *     city_id="exists:cities,id")
     */
    public function update(Request $request, CustomerUserLegalInfo $customer_user_legal_info): RedirectResponse
    {
        $customer_user_legal_info->update($request->all());
        return History::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(CustomerUserLegalInfo $customer_user_legal_info): RedirectResponse
    {
        $customer_user_legal_info->delete();
        return back();
    }

    public function getModel(): ?string
    {
        return CustomerUserLegalInfo::class;
    }
}
