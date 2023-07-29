<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HistoryHelper;
use App\Helpers\SystemMessageHelper;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\SMSManager\ConfigProvider;
use App\Utils\SMSManager\Exceptions\SMSDriverInvalidConfigurationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class SMSDriverController extends BaseController
{

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function edit(): Factory|View|Application
    {
        $drivers = ConfigProvider::getAll();
        return view("admin.pages.sms-driver.edit")->with([
            "drivers" => $drivers
        ]);
    }

    /**
     * @rules(drivers="required|array",
     * dynamic_rules=\App\Utils\SMSManager\ConfigProvider::getRules(request('drivers')))
     * @role(super_user, cms_manager, acc_manager)
     */
    public function update(Request $request): RedirectResponse
    {
        $drivers = $request->get("drivers");
        try {
            ConfigProvider::setAll($drivers);
            return HistoryHelper::redirectBack();
        } catch (NotValidSettingRecordException $e) {
            SystemMessageHelper::addErrorMessage('system_messages.sms_driver.invalid_record');
            return redirect()->back()->withInput();
        } catch (SMSDriverInvalidConfigurationException $e) {
            SystemMessageHelper::addErrorMessage('system_messages.sms_driver.invalid_driver');
            return redirect()->back()->withInput();
        }
    }

    public function getModel(): ?string
    {
        return null;
    }
}
