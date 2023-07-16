<?php

namespace App\Http\Controllers\Admin;

use App\Services\Common\SSHService;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\SystemUpgrade\SystemUpgradeSettingService;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UpgradeController extends BaseController
{

    public function index(Request $request): Factory|View|Application {
        $record = SystemUpgradeSettingService::getRecord();
        $larammerce_repo_address = $record->getLarammerceRepoAddress();
        $larammerce_theme_repo_address = $record->getLarammerceThemeRepoAddress();
        if ($request->session()->has("public_key")) {
            $public_key = $request->session()->get("public_key");
            return view("admin.pages.upgrade.index", compact("larammerce_theme_repo_address", "larammerce_repo_address", "public_key"));
        }
        return view("admin.pages.upgrade.index", compact("larammerce_theme_repo_address", "larammerce_repo_address"));
    }

    /**
     * @rules(larammerce_repo_address="required|regex:/^git@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9\-]+:[a-zA-Z0-9\-]+\/[a-zA-Z0-9\-.]+\.git$/",
     *     larammerce_theme_repo_address="required|regex:/^git@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9\-]+:[a-zA-Z0-9\-]+\/[a-zA-Z0-9\-.]+\.git$/")
     */
    public function saveConfig(Request $request) {
        $larammerce_repo_address = $request->get('larammerce_repo_address');
        $larammerce_theme_repo_address = $request->get('larammerce_theme_repo_address');

        $record = SystemUpgradeSettingService::getRecord();
        $record->setLarammerceRepoAddress($larammerce_repo_address);
        $record->setLarammerceThemeRepoAddress($larammerce_theme_repo_address);

        $domains = $this->extractDomains([$larammerce_repo_address, $larammerce_theme_repo_address]);

        if ($request->has("create_key")) {
            $public_key = SSHService::addSSHKey($domains);
        }

        try {
            SystemUpgradeSettingService::setRecord($record);
            if (isset($public_key)) {
                return History::redirectBack()->with("public_key", $public_key);
            } else {
                return History::redirectBack();
            }
        } catch (NotValidSettingRecordException $e) {
            SystemMessageService::addErrorMessage('system_messages.system_upgrade.invalid_record');
            return History::redirectBack()->withInput();
        }
    }

    private function extractDomains($repo_urls): array {
        $domains = [];

        foreach ($repo_urls as $url) {
            if (preg_match('/^git@([a-zA-Z0-9.-]+):.+$/', $url, $matches)) {
                $domains[] = $matches[1];
            }
        }

        return array_unique($domains);
    }

    public function getModel(): ?string {
        return null;
    }
}
