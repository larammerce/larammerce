<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HistoryHelper;
use App\Models\ModalRoute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Response;


class ModalRouteController extends BaseController
{
    public function index()
    {

    }

    public function create()
    {

    }

    /**
     * @role(super_user, cms_manager)
     * @rules(route="required|string", modal_id="required|exists:modals,id", children_included="bool", self_included="bool")
     */
    public function store(Request $request): RedirectResponse
    {
        ModalRoute::create($request->all());
        return HistoryHelper::redirectBack();
    }

    public function edit(Request $request, ModalRoute $modal_route)
    {

    }

    /**
     * @role(super_user, cms_manager)
     * @rules(route="required|string", children_included="bool", self_included="bool")
     */
    public function update(Request $request, ModalRoute $modal_route): RedirectResponse|Response
    {
        $modal_route->update($request->all());
        return HistoryHelper::redirectBack();
    }

    public function destroy(ModalRoute $modal_route): RedirectResponse
    {
        $modal_route->delete();
        return HistoryHelper::redirectBack();
    }


    public function getModel(): ?string
    {
        return ModalRoute::class;
    }
}
