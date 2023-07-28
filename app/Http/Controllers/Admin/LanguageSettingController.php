<?php

namespace App\Http\Controllers\Admin;

use App\Features\Language\LanguageConfig;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
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
class LanguageSettingController extends BaseController
{

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function edit(): Factory|View|Application
    {
        $languages = LanguageConfig::getAll();
        return view("admin.pages.language.edit")->with([
            "languages" => $languages
        ]);
    }

    /**
     * @rules(languages="required|array",
     * dynamic_rules=\App\Services\FeatureConfig\FeatureConfig\Language\LanguageConfig::getRules(request('languages')))
     * @role(super_user, cms_manager, acc_manager)
     */
    public function update(Request $request): RedirectResponse
    {
        $languages = $request->get("languages");
        try {
            LanguageConfig::setAll($languages);
            return History::redirectBack();
        } catch (NotValidSettingRecordException $e) {
            SystemMessageService::addErrorMessage('system_messages.language.invalid_record');
            return redirect()->back()->withInput();
        }
    }

    public function getModel(): ?string
    {
        return null;
    }
}
