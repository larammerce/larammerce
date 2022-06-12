<?php

namespace App\Http\Controllers\Admin;

use App\Models\Modal;
use App\Models\ModalRoute;
use App\Utils\Common\History;
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
        return History::redirectBack();
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
        return History::redirectBack();
    }

    public function destroy(ModalRoute $modal_route): RedirectResponse
    {
        $modal_route->delete();
        return History::redirectBack();
    }


    public function getModel(): ?string
    {
        return ModalRoute::class;
    }
}
