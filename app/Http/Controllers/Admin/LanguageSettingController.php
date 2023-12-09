<?php

namespace App\Http\Controllers\Admin;

use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\Language\LanguageSettingService;
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
        $languages = LanguageSettingService::getAll();
        return view("admin.pages.language.edit")->with([
            "languages" => $languages
        ]);
    }

    /**
     * @rules(languages="required|array",
     * dynamic_rules=\App\Utils\CMS\Setting\Language\LanguageSettingService::getRules(request('languages')))
     * @role(super_user, cms_manager, acc_manager)
     */
    public function update(Request $request): RedirectResponse
    {
        $languages = $request->get("languages");
        try {
            LanguageSettingService::setAll($languages);
            return History::redirectBack();
        } catch (NotValidSettingRecordException $e) {
            SystemMessageService::addErrorMessage('system_messages.language.invalid_record');
            return redirect('language.edit');
        }
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function add(): View
    {
        $available_languages = LanguageSettingService::getAvailableChoices();
        return view('admin.pages.language.create',
            [
                'available_languages' => $available_languages
            ]
        );
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            LanguageSettingService::setOne(
                $request->input('language_id'),
                $request->has('is_enabled'),
                $request->has('is_default'),
            );
            SystemMessageService::addSuccessMessage('زبان با موفقیت اضافه شد');
            return redirect()->back();
        } catch (\Throwable $e) {
            SystemMessageService::addErrorMessage($e->getMessage());
            return History::redirectBack();
        }
    }

    public function getModel(): ?string
    {
        return null;
    }
}
