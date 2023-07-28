<?php

namespace App\Http\Controllers\Admin;


use App\Features\SystemLog\ActionLogConfig;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class ActionLogSettingController extends BaseController
{
    public function edit(): Factory|View|Application
    {
        $record = ActionLogConfig::getRecord();
        $is_enabled = $record->getIsEnabled();
        $log_period = $record->getLogPeriod();
        $enabled_controllers = $record->getEnabledControllers();
        $existing_controllers = Arr::except(get_controller_entity_names(),
            ['App\Http\Controllers\Admin\BaseController','App\Http\Controllers\Admin\ActionLogController']);
        return view("admin.pages.action-log-setting.edit")->with([
            "is_enabled" => $is_enabled,
            "log_period" => $log_period,
            "existing_controllers" => $existing_controllers,
            "enabled_controllers" => $enabled_controllers
        ]);

    }

    public function update(Request $request): RedirectResponse
    {

        $is_enabled = $request->get('is_enabled');
        $log_period = $request->get('log_period');
        $enabled_controllers = json_decode($request->get('enabled_controllers'), true);

        $record = ActionLogConfig::getRecord();
        $record->setIsEnabled($is_enabled);
        $record->setLogPeriod($log_period);
        $record->setEnabledControllers($enabled_controllers);
        //dd($enabled_controllers);
        try {
            ActionLogConfig::setRecord($record);
            return History::redirectBack();
        } catch (NotValidSettingRecordException $e) {
            SystemMessageService::addErrorMessage('system_messages.action_log_setting.invalid_record');
            return History::redirectBack()->withInput();
        }
    }


    public function getModel(): ?string
    {
        return null;
    }
}
