<?php

namespace App\Utils\CMS;

use App\Exceptions\General\InvalidActionRequestException;
use App\Jobs\SaveSystemLog;
use App\Models\ActionLog;
use App\Models\User;
use App\Utils\CMS\Setting\SystemLog\ActionLogSettingService;
use App\Utils\Reflection\Action;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActionLogService
{
    public static function saveAction(Action $action, User $user, bool $is_allowed = true)
    {
        try {
            if (ActionLogSettingService::isControllerEnabled($action->getClassName())) {
                $request = $action->getRequest();
                $action_log = new ActionLog();
                if (isset($request) and $request instanceof Request) {
                    $action_log->user_agent_ip = $request->getClientIp();
                    $action_log->user_agent_title = $request->userAgent();
                    $action_log->request_data = $request->all();
                    $action_log->url_parameters = $request->route()->parameters();
                    if (isset($action_log->url_parameters) and
                        sizeof($action_log->url_parameters) > 0 and
                        isset($action_log->url_parameters[0]) and
                        isset($action_log->url_parameters[0]->id))
                        $action_log->related_model_id = $action_log->url_parameters[0]->id;
                    else
                        $action_log->related_model_id = null;
                } else
                    throw new InvalidActionRequestException();
                $action_log->user = $user;
                $action_log->is_allowed = $is_allowed;
                $action_log->action = $action->getAction();
                $arr = null;
                if ((strlen($action_log->action) > 0) and str_contains($action_log->action, "@"))
                    $arr = explode('@', $action_log->action);
                if ($arr != null and is_array($arr) and sizeof($arr) > 1)
                    $action_log->related_model_type = get_controller_entity_name($arr[0]);
                else
                    $action_log->related_model_type = null;
                $job = new SaveSystemLog($action_log);
                dispatch($job);
            }
        } catch (InvalidActionRequestException $exception) {
            Log::error("ActionLogService.saveAction: invalid request : " . $exception->getMessage());
        } catch (Exception $exception) {
            Log::error("ActionLogService.saveAction: failed : " . $exception->getMessage());
        }
    }
}
