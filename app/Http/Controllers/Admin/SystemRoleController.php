<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HistoryHelper;
use App\Helpers\RequestHelper;
use App\Models\SystemRole;
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
class SystemRoleController extends BaseController
{
    /**
     * @role(super_user, cms_manager)
     */
    public function index(): View|Factory|JsonResponse|Application
    {
        parent::setPageAttribute();

        if (RequestHelper::isRequestAjax()) {
            return response()->json(SystemRole::all());
        }

        $roles = SystemRole::with('users')
            ->paginate(SystemRole::getPaginationCount());
        return view('admin.pages.system-role.index', compact('roles'));
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function create(): Factory|View|Application
    {
        return view('admin.pages.system-role.create');
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(name="required|unique:system_roles,name")
     */
    public function store(Request $request): RedirectResponse
    {
        SystemRole::create($request->all());
        return redirect()->route('admin.system-role.index');
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(SystemRole $system_role): Factory|View|Application
    {
        return view('admin.pages.system-role.edit')->with(['role' => $system_role]);
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(name="required|unique:system_roles,name," . request()->get('id'))
     */
    public function update(Request $request, SystemRole $system_role): RedirectResponse
    {
        $system_role->update($request->all());
        return HistoryHelper::redirectBack();
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function destroy(SystemRole $system_role): RedirectResponse
    {
        $system_role->delete();
        return redirect()->route('admin.system-role.index');
    }


    public function getModel(): ?string
    {
        return SystemRole::class;
    }
}
