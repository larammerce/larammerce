<?php

namespace App\Http\Controllers\Admin;

use App\Models\SystemUser;
use App\Utils\Common\History;
use App\Utils\Common\MessageFactory;
use App\Utils\Common\RequestService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class SystemUserController extends BaseController
{
    /**
     * @role(super_user)
     */
    public function index(): Factory|View|Application
    {
        $system_users = SystemUser::with('user', 'roles')
            ->paginate(SystemUser::getPaginationCount());
        return view('admin.pages.system-user.index', compact('system_users'));
    }

    /**
     * @role(super_user)
     */
    public function create(): Factory|View|Application
    {
        return view('admin.pages.system-user.create');
    }

    /**
     * @role(super_user)
     * @rules(user_id="required|exists:users,id", is_super_user="boolean")
     */
    public function store(Request $request): RedirectResponse
    {
        $system_user = SystemUser::create($request->all());
        if ($request->hasFile('image'))
            $system_user->setImagePath();
        return redirect()->route('admin.pages.system-user.index');
    }

    /**
     * @role(super_user)
     */
    public function edit(SystemUser $system_user): Factory|View|Application
    {
        $system_user->load('user');
        return view('admin.pages.system-user.edit')->with(compact("system_user"));
    }

    /**
     * @role(super_user)
     * @rules(is_super_user="boolean")
     */
    public function update(Request $request, SystemUser $system_user): RedirectResponse
    {
        $system_user->update($request->all());
        if ($request->hasFile('image'))
            $system_user->setImagePath();
        return History::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(SystemUser $system_user): RedirectResponse
    {
        $system_user->delete();
        return redirect()->route('admin.system-user.index');
    }

    /**
     * @role(super_user)
     */
    public function removeImage(SystemUser $system_user): RedirectResponse
    {
        $system_user->update([
            "main_image_path" => null
        ]);
        return back();
    }

    /**
     * @role(super_user)
     * @rules(roles="array", roles.*="exists:system_roles,id")
     */
    public function attachRoles(Request $request, SystemUser $system_user): RedirectResponse
    {
        $system_user->roles()->detach();
        $system_user->roles()->attach($request->get('roles'));
        return redirect()->route('admin.system-user.index');
    }

    /**
     * @role(super_user)
     * @rules(id="required|exists:system_roles,id")
     */
    public function attachRole(Request $request, SystemUser $system_user): JsonResponse|RedirectResponse
    {
        $system_user->roles()->attach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.system_user.role_attached'], 200, compact('system_user')
            ), 200);
        }
        return redirect()->route('admin.system-user.index');
    }

    /**
     * @role(super_user)
     * @rules(id="required|exists:system_roles,id")
     */
    public function detachRole(Request $request, SystemUser $system_user): JsonResponse|RedirectResponse
    {
        $system_user->roles()->detach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.system_user.role_detached'], 200, compact('system_user')
            ), 200);
        }
        return redirect()->route('admin.system-user.index');
    }


    public function getModel(): ?string
    {
        return SystemUser::class;
    }
}
