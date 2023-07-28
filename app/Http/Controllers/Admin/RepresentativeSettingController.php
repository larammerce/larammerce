<?php

namespace App\Http\Controllers\Admin;

use App\Features\Representative\RepresentativeConfig;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RepresentativeSettingController extends BaseController
{

    public function edit(Request $request): Factory|View|Application
    {
        $representative_setting = RepresentativeConfig::getRecord();
        return view("admin.pages.representative.edit", compact("representative_setting"));
    }

    /**
     * @rules(is_enabled="required|bool", is_customer_representative_enabled="required|bool", options.*="string")
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            RepresentativeConfig::update(
                $request->get("is_enabled"),
                $request->get("is_customer_representative_enabled"),
                $request->get("options")
            );

            return History::redirectBack();
        } catch (NotValidSettingRecordException $e) {
            return redirect()->back()->withInput();
        }
    }

    public function getModel(): ?string
    {
        return null;
    }
}
