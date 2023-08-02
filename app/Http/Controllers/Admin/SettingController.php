<?php

namespace App\Http\Controllers\Admin;

use App\Interfaces\Repositories\SettingRepositoryInterface;
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
class SettingController extends BaseController {
    private SettingRepositoryInterface $setting_repository;

    public function __construct(SettingRepositoryInterface $setting_repository) {
        parent::__construct();

        $this->setting_repository = $setting_repository;
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function index(): Factory|View|Application {
        parent::setPageAttribute();
        $settings = $this->setting_repository->getAllCMSRecordsPaginated();
        return view('admin.pages.setting.index', compact('settings'));
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function create(): Factory|View|Application {
        return view('admin.pages.setting.create');
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(key="required", value="required", is_private="boolean")
     */
    public function store(Request $request): RedirectResponse {
        if ($request->has('is_private'))
            RequestService::setAttr('user_id', auth('web')->id());
        $this->setting_repository->create(
            $request->get("key"),
            $request->get("value")
        );
        return redirect()->route('admin.setting.index');
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(Setting $setting): Factory|View|Application {
        $setting->load('user');
        return view('admin.pages.setting.edit')->with(['setting' => $setting]);
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(key="required", value="required")
     */
    public function update(Request $request, Setting $setting): RedirectResponse {
        RequestService::setAttr('user_id', $request->get('is_private') ? auth('web')->id() : null);
        $this->setting_repository->update(
            $setting,
            $request->get("key"),
            $request->get("value")
        );
        return History::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(Setting $setting): RedirectResponse {
        $this->setting_repository->delete($setting);
        return back();
    }


    public function getModel(): ?string {
        return Setting::class;
    }
}
