<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use App\Utils\Common\History;
use App\Utils\Common\RequestService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class SettingController extends BaseController
{
    /**
     * @role(super_user, cms_manager)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $settings = Setting::with(['user'])->userSettings()->nonSystemSettings()->orderBy("key", "ASC")
            ->paginate(Setting::getPaginationCount());
        return view('admin.pages.setting.index', compact('settings'));
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function create(): Factory|View|Application
    {
        return view('admin.pages.setting.create');
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(key="required", value="required", is_private="boolean")
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->has('is_private'))
            RequestService::setAttr('user_id', auth('web')->id());
        Setting::create($request->all());
        return redirect()->route('admin.setting.index');
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(Setting $setting): Factory|View|Application
    {
        $setting->load('user');
        return view('admin.pages.setting.edit')->with(['setting' => $setting]);
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(key="required", value="required")
     */
    public function update(Request $request, Setting $setting): RedirectResponse
    {
        RequestService::setAttr('user_id', $request->get('is_private') ? auth('web')->id() : null);
        $setting->update($request->all());
        return History::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(Setting $setting): RedirectResponse
    {
        $setting->delete();
        return back();
    }


    public function getModel(): ?string
    {
        return Setting::class;
    }
}
