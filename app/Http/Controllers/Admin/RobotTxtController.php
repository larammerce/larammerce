<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HistoryHelper;
use App\Models\SystemUser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class RobotTxtController extends BaseController
{
    private static string $FILE_PATH;

    public function __construct()
    {
        parent::__construct();
        RobotTxtController::$FILE_PATH = public_path() . "/robots.txt";
        $this->middleware('robot-txt-lock');
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function create(): RedirectResponse
    {
        if (!File::exists(RobotTxtController::$FILE_PATH)) {
            $data = "User-agent: * \nDisallow: \n\n\nUser-agent: Googlebot";
            if (request()->has("user-id")) {
                $system_user = SystemUser::find(request()->get("user-id"));
                if ($system_user != null and $system_user->user != null)
                    $data = static::patchAuthorSign($system_user->user->username, $data);
            }
            File::put(RobotTxtController::$FILE_PATH, $data);
        }
        return HistoryHelper::redirectBack();
    }

    private static function patchAuthorSign($sign, $data): string
    {
        $current_date = date("l jS \of F Y h:i:s A");
        if (preg_match("/#Last edit by(.*)/", $data))
            $data = preg_replace("/#Last edit by(.*)/", "", $data);
        return trim($data) . "\n\n#Last edit by " . $sign . " at " . $current_date . ".";
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function store(Request $request): RedirectResponse
    {
        return $this->update($request, $request->get('user-id'));
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     * @rules(robot="required")
     */
    public function update(Request $request, $user_id): RedirectResponse
    {
        $data = $request->get('robot');
        if ($user_id != null) {
            $system_user = SystemUser::find($user_id);
            if ($system_user != null and $system_user->user != null)
                $data = static::patchAuthorSign($system_user->user->username, $data);
        }
        file_put_contents(RobotTxtController::$FILE_PATH, $data);
        return HistoryHelper::redirectBack();
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function show(): Factory|View|Application
    {
        return $this->index();
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function index(): Factory|View|Application
    {
        $file_content = null;
        if (File::exists(RobotTxtController::$FILE_PATH))
            $file_content = file_get_contents(RobotTxtController::$FILE_PATH, 'r');
        return view('admin.pages.robot-txt.index', compact('file_content'));
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function edit(): Factory|View|Application
    {
        $file_content = null;
        if (File::exists(RobotTxtController::$FILE_PATH))
            $file_content = file_get_contents(RobotTxtController::$FILE_PATH, 'r');
        return view('admin.pages.robot-txt.edit', compact('file_content'));
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function destroy(): RedirectResponse
    {
        if (File::exists(RobotTxtController::$FILE_PATH) and
            File::delete(RobotTxtController::$FILE_PATH))
            return HistoryHelper::redirectBack();
        return redirect()->back()->with('error');
    }

    public function getModel(): ?string
    {
        return null;
    }
}
