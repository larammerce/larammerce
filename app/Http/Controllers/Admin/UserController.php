<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class UserController extends BaseController
{
    /**
     * @role(super_user)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $users = User::with('systemUser', 'customerUser')
            ->paginate(User::getPaginationCount());
        return view('admin.pages.user.index', compact('users'));
    }

    /**
     * @role(super_user)
     */
    public function create(): Factory|View|Application
    {
        return view('admin.pages.user.create');
    }

    /**
     * @role(super_user)
     * @rules(name='required', family='required', username='required|unique:users', email='required|unique:users',
    password='required|min:6|confirmed', password_confirmation='required|min:6', is_system_user='required|boolean',
    is_customer_user='required|boolean')
     */
    public function store(Request $request): RedirectResponse
    {
        $requestData = $request->all();
        $requestData["password"] = bcrypt($requestData["password"]);
        $isCustomerUser = $requestData["is_customer_user"];
        $requestData["is_customer_user"] = false;

        $user = User::create($requestData);

        if ($isCustomerUser)
            return redirect()->to(route('admin.customer-user.create') . "?id={$user->id}");

        return redirect()->route('admin.user.edit', $user);
    }

    /**
     * @role(super_user)
     */
    public function edit(User $user): Factory|View|Application
    {
        $user->load('systemUser', 'customerUser');
        return view('admin.pages.user.edit', compact('user'));
    }

    /**
     * @role(super_user)
     * @rules(name='required', family='required',
     *     username='required|unique:users,username,'. request()->get('id'),
     *     email='required|unique:users,email,'. request()->get('id'), password='min:6|confirmed',
     *     password_confirmation='min:6', is_system_user='boolean', is_customer_user='boolean')
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $oldCustomerUser = $user->is_customer_user;

        $requestData = $request->all();
        $requestData["password"] = bcrypt($requestData["password"]);
        $isCustomerUser = $requestData["is_customer_user"];
        $requestData["is_customer_user"] = (($oldCustomerUser == $isCustomerUser) ? $isCustomerUser : false);

        $user->update($requestData);

        if (($oldCustomerUser != $isCustomerUser) and $isCustomerUser)
            return redirect()->to(route('admin.customer-user.create') . "?id={$user->id}");

        return History::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return back();
    }

    /**
     */
    public function getModel(): ?string
    {
        return User::class;
    }
}
