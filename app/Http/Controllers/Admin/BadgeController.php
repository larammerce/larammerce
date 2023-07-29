<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HistoryHelper;
use App\Helpers\RequestHelper;
use App\Models\Badge;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Response;


class BadgeController extends BaseController
{
    /**
     * @role(super_user, cms_manager)
     */
    public function index(): View|Factory|JsonResponse|Application
    {
        if (RequestHelper::isRequestAjax()) {
            return response()->json(Badge::all());
        }

        parent::setPageAttribute();
        $badges = Badge::paginate(Badge::getPaginationCount());
        return view('admin.pages.badge.index', compact('badges'));
    }

    public function create(): Factory|View|Application
    {
        return view('admin.pages.badge.create');
    }

    /**
     * @rules(title="required")
     */
    public function store(Request $request): RedirectResponse
    {
        $badge = Badge::create($request->all());
        if ($request->hasFile('image'))
            $badge->setImagePath();
        return redirect()->route('admin.badge.index');
    }


    public function show(Badge $badge): Factory|View|Application
    {
        return response("there is no show page yet.");
    }


    public function edit(Badge $badge): Factory|View|Application
    {
        return view('admin.pages.badge.edit', ["badge" => $badge]);
    }


    public function update(Request $request, Badge $badge): RedirectResponse|Response
    {
        //dd($badge->hasImage());

        $badge->update($request->all());

        if ($request->hasFile('image')) {
            $badge->setImagePath();
        }

        return HistoryHelper::redirectBack();
    }

    public function destroy(Badge $badge): RedirectResponse
    {
        if ($badge->hasImage()) {
            $badge->removeImage();
        }
        $badge->delete();
        return HistoryHelper::redirectBack();
    }


    public function removeImage(Badge $badge): RedirectResponse
    {
        $badge->removeImage();
        return back();
    }


    public function getModel(): ?string
    {
        return Badge::class;
    }
}
