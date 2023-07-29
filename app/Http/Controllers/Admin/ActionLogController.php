<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SystemMessageHelper;
use App\Models\ActionLog;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Class ActionLogController
 * @role(enabled=true)
 * @package App\Http\Controllers\Admin
 */
class ActionLogController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @role(super_user, stock_manager, acc_manager)
     * @rules(user_id="exists:users,id", date_time="date")
     */
    public function index(Request $request): View|Response|RedirectResponse
    {
        try {
            $user = null;
            if (request()->has('user_id')) {
                parent::setPageAttribute(request()->get("user_id"));
                $user = User::find(request()->get('user_id'));
                $action_logs = ActionLog::where("user_id", $user->id)->paginate(ActionLog::getPaginationCount());
            } else {
                parent::setPageAttribute();
                if ($request->has('date_time')) {
                    $date_time = $request->get('date_time');
                    $action_logs = ActionLog::where('created_at', $date_time)
                        ->paginate(ActionLog::getPaginationCount());
                } else {
                    $action_logs = ActionLog::orderBy('created_at', 'ASC')->paginate(ActionLog::getPaginationCount());
                }
            }
            return view('admin.pages.action-log.index',
                compact('action_logs', 'user'));
        } catch (Exception $exception) {
            Log::error("action_log.index.exception : " . $exception->getMessage());
            SystemMessageHelper::addErrorMessage('system_messages.action_log.unable_to_retrieve_data');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     * @role(super_user, stock_manager, acc_manager)
     */
    public function show(ActionLog $action_log): View|Response
    {
        return view('admin.pages.action-log.show', compact('action_log'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @role(super_user)
     * @throws Exception
     */
    public function destroy(ActionLog $action_log): View|Response|RedirectResponse
    {
        $action_log->delete();
        return back();
    }

    public function getModel(): ?string
    {
        return ActionLog::class;
    }

    public function filter(Request $request): View|Response
    {
        $terms = [];
        if ($request->get("_id") != null)
            $terms['_id'] = $request->get("_id");
        if ($request->get("user_id") != null)
            $terms['user_id'] = $request->get("user_id");
        if ($request->get("related_model_type") != null)
            $terms['related_model_type'] = $request->get("related_model_type");
        if ($request->get("related_model_id") != null)
            $terms['related_model_id'] = $request->get("related_model_id");
        if ($request->get("first_date") != null and $request->get("last_date") != null)
            $terms['created_at'] = [Carbon::parse($request->get("first_date")),
                Carbon::parse($request->get("last_date"))];
        $action_logs = ActionLog::filter($terms)->paginate(ActionLog::getPaginationCount());
        return view('admin.pages.action-log.index',
            compact('action_logs'));
    }
}
