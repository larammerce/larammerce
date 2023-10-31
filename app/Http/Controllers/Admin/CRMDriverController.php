<?php

namespace App\Http\Controllers\Admin;

use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\History;
use App\Utils\CRMManager\ConfigProvider;
use App\Utils\CRMManager\Exceptions\CRMDriverInvalidConfigurationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class CRMDriverController extends BaseController
{

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function edit(): Factory|View|Application
    {
        $drivers = ConfigProvider::getAll();
        return view("admin.pages.crm-driver.edit")->with([
            "drivers" => $drivers
        ]);
    }

    /**
     * @rules(drivers="required|array",
     * dynamic_rules=\App\Utils\CRMManager\ConfigProvider::getRules(request('drivers')))
     * @role(super_user, cms_manager, acc_manager)
     */
    public function update(Request $request): RedirectResponse
    {
        $drivers = $request->get("drivers");
        try {
            ConfigProvider::setAll($drivers);
            return History::redirectBack();
        } catch (NotValidSettingRecordException $e) {
            SystemMessageService::addErrorMessage('system_messages.crm_driver.invalid_record');
            return redirect()->back()->withInput();
        } catch (CRMDriverInvalidConfigurationException $e) {
            SystemMessageService::addErrorMessage('system_messages.crm_driver.invalid_driver');
            return redirect()->back()->withInput();
        }
    }

    public function getModel(): ?string
    {
        return null;
    }
}
