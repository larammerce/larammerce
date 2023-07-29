<?php

namespace App\Http\Controllers\Admin;

use App\Features\Survey\SurveyConfig;
use App\Features\Survey\SurveySettingData;
use App\Helpers\HistoryHelper;
use App\Helpers\SystemMessageHelper;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class SurveyController extends BaseController
{

    public function edit(): Factory|View|Application
    {
        $survey_setting_record = SurveyConfig::getRecord();
        return view("admin.pages.survey.edit")->with([
            "survey" => $survey_setting_record
        ]);
    }

    /**
     * @rules(default_delay_hours="required|integer|min:0|max:24",
     *        default_delay_days="required|integer|min:0",
     *        default_survey_url="required|url",
     *        custom_states="array",
     *        custom_states.*.state_id="exists:states,id",
     *        custom_states.*.custom_delay_days="integer|min:0",
     *        custom_states.*.custom_delay_hours="integer|min:0|max:24",
     *        custom_states.*.custom_survey_url="url")
     */
    public function update(Request $request): RedirectResponse
    {
        $record = new SurveySettingData();
        $record->setDefaultDelayDays($request->get("default_delay_days"));
        $record->setDefaultDelayHours($request->get("default_delay_hours"));
        $record->setDefaultSurveyUrl($request->get("default_survey_url"));

        if (is_array($request->get("custom_states")))
            foreach ($request->get("custom_states") as $item) {
                if (isset($item["state_id"]) and $item["state_id"] !== null and
                    ((is_string($item["state_id"]) and strlen($item["state_id"]) > 0) or
                        (is_integer($item["state_id"]) and $item["state_id"] > 0)))
                    $record->putCustomState($item["state_id"], $item["custom_delay_days"],
                        $item["custom_delay_hours"], $item["custom_survey_url"]);
            }

        try {
            SurveyConfig::setRecord($record);
            return HistoryHelper::redirectBack();
        } catch (NotValidSettingRecordException $e) {
            SystemMessageHelper::addErrorMessage('system_messages.survey.invalid_record');
            return redirect()->back()->withInput();
        }

    }

    public function getModel(): ?string
    {
        return null;
    }
}
