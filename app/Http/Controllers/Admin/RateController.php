<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rate;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class RateController extends BaseController
{
    /**
     * @role(super_user, cms_manager)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $rates = Rate::with('object')->whereRaw(DB::raw("CHAR_LENGTH(comment) > 0"))
            ->paginate(Rate::getPaginationCount());
        return view('admin.pages.rate.index', compact('rates'));
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(Rate $rate): Factory|View|Application
    {
        $rate->update(["is_reviewed" => 1]);
        $rate->load('object');
        return view('admin.pages.rate.edit')->with(['rate' => $rate]);
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function update(Request $request, Rate $rate): RedirectResponse
    {
        $rate->update($request->all());
        $rate->save();
        return History::redirectBack();
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function destroy(Rate $rate): RedirectResponse
    {
        $rate->delete();
        return back();
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function changeAcceptState(Rate $rate): RedirectResponse
    {
        $rate->is_accepted = !$rate->is_accepted;
        $rate->save();
        return back();
    }

    /**
     */
    public function getModel(): ?string
    {
        return Rate::class;
    }
}
