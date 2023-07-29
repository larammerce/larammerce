<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HistoryHelper;
use App\Helpers\RequestHelper;
use App\Models\Modal;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Response;


class ModalController extends BaseController
{
    /**
     * @role(super_user, cms_manager)
     */
    public function index(): View|Factory|JsonResponse|Application
    {
        if (RequestHelper::isRequestAjax()) {
            return response()->json(Modal::all());
        }

        parent::setPageAttribute();
        $modals = Modal::paginate(Modal::getPaginationCount());
        return view('admin.pages.modal.index', compact('modals'));
    }

    public function create(): Factory|View|Application
    {
        return view('admin.pages.modal.create');
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(title="required|string",repeat_count="required|integer",size_class="required|string")
     */
    public function store(Request $request): RedirectResponse
    {
        $modal = Modal::create($request->all());
        if ($request->hasFile('image'))
            $modal->setImagePath();
        return redirect()->route('admin.modal.edit', compact('modal'));
    }


    public function show(Modal $modal): Factory|View|Application
    {
        return response("there is no show page yet.");
    }


    public function edit(Modal $modal): Factory|View|Application
    {
        return view('admin.pages.modal.edit', ["modal" => $modal]);
    }


    /**
     * @role(super_user, cms_manager)
     * @rules(title="required|string",repeat_count="required|integer",size_class="required|string")
     */
    public function update(Request $request, Modal $modal): RedirectResponse|Response
    {
        $modal->update($request->all());

        if ($request->hasFile('image')) {
            $modal->setImagePath();
        }

        return HistoryHelper::redirectBack();
    }

    public function destroy(Modal $modal): RedirectResponse
    {
        if ($modal->hasImage()) {
            $modal->removeImage();
        }
        $modal->delete();
        return HistoryHelper::redirectBack();
    }


    public function removeImage(Modal $modal): RedirectResponse
    {
        $modal->removeImage();
        return back();
    }


    public function getModel(): ?string
    {
        return Modal::class;
    }
}
