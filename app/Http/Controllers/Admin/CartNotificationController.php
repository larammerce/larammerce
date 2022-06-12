<?php

namespace App\Http\Controllers\Admin;

use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\CartNotification\CartNotificationModel;
use App\Utils\CMS\Setting\CartNotification\CartNotificationService;
use App\Utils\CMS\SystemMessageService;
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
class CartNotificationController extends BaseController
{

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function edit(): Factory|View|Application
    {
        $cart_notification_setting_record = CartNotificationService::getRecord();
        return view("admin.pages.cart-notification.edit")->with([
            "cart_notification" => $cart_notification_setting_record
        ]);
    }

    /**
     * @rules(is_active="required|bool",
     *        default_delay_hours="required|integer|min:0|max:24",
     *        notify_with_email="required|bool",
     *        notify_with_sms="required|bool")
     * @role(super_user)
     */
    public function update(Request $request): RedirectResponse
    {
        $record = new CartNotificationModel();
        $record->setIsActive($request->get("is_active"));
        $record->setDefaultDelayHours($request->get("default_delay_hours"));
        $record->setNotifyWithEmail($request->get("notify_with_email"));
        $record->setNotifyWithSMS($request->get("notify_with_sms"));

        try {
            CartNotificationService::setRecord($record);
            return History::redirectBack();
        } catch (NotValidSettingRecordException $e) {
            SystemMessageService::addErrorMessage('system_messages.cart_notification.invalid_record');
            return redirect()->back()->withInput();
        }

    }

    public function getModel(): ?string
    {
        return null;
    }
}
