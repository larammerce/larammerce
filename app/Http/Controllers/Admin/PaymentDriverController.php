<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HistoryHelper;
use App\Helpers\SystemMessageHelper;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\PaymentManager\ConfigProvider;
use App\Utils\PaymentManager\Exceptions\PaymentInvalidDriverException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class PaymentDriverController extends BaseController
{

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function edit(): Factory|View|Application
    {
        $drivers = ConfigProvider::getAll();
        return view("admin.pages.payment-driver.edit")->with([
            "drivers" => $drivers
        ]);
    }

    /**
     * @rules(drivers="required|array",
     * dynamic_rules=\App\Utils\PaymentManager\ConfigProvider::getRules(request('drivers')))
     * @role(super_user, cms_manager, acc_manager)
     */
    public function update(Request $request): RedirectResponse
    {
        $drivers = $request->file("drivers") != null ?
            array_merge_recursive($request->get("drivers"),
            $request->file("drivers")) : $request->get("drivers");
        try {
            ConfigProvider::setAll($drivers);
            return HistoryHelper::redirectBack();
        } catch (NotValidSettingRecordException $e) {
            SystemMessageHelper::addErrorMessage('system_messages.payment_driver.invalid_record');
            return redirect()->back()->withInput();
        } catch (PaymentInvalidDriverException $e) {
            SystemMessageHelper::addErrorMessage('system_messages.payment_driver.invalid_driver');
            return redirect()->back()->withInput();
        }
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function removeFile(string $driver_id): RedirectResponse
    {
        try {
            ConfigProvider::removeFile($driver_id);
        } catch (NotValidSettingRecordException $e) {
            SystemMessageHelper::addErrorMessage('system_messages.payment_driver.invalid_record');
            return back()->withInput();
        } catch (PaymentInvalidDriverException $e) {
            SystemMessageHelper::addErrorMessage('system_messages.payment_driver.invalid_driver');
            return back()->withInput();
        }
        return back();
    }

    public function getModel(): ?string
    {
        return null;
    }
}
