<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\State;
use App\Utils\Common\History;
use App\Utils\Common\RequestService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class CityController extends BaseController
{
    /**
     * @role(super_user, cms_manager)
     * @rules(state_id="exists:states,id")
     */
    public function index(): View|Factory|JsonResponse|Application
    {
        $state = null;
        if (request()->has('state_id')) {
            $state = State::find(request()->get('state_id'));
            parent::setPageAttribute($state->id);
            if (RequestService::isRequestAjax())
                return response()->json($state->cities);
            $cities = $state->cities()->with('state')->paginate(City::getPaginationCount());
        } else {
            parent::setPageAttribute();
            $cities = City::with('state')->paginate(City::getPaginationCount());
        }
        return view('admin.pages.city.index', compact('cities', 'state'));
    }

    /**
     * @role(super_user)
     * @rules(state_id="required|exists:states,id")
     */
    public function create(): Factory|View|Application
    {
        $state = State::find(request()->get('state_id'));
        return view('admin.pages.city.create', compact('state'));
    }

    /**
     * @role(super_user)
     * @rules(name="required|unique:cities,name", state_id="required|exists:states,id", has_district="boolean")
     */
    public function store(Request $request): RedirectResponse
    {
        $city = City::create($request->all());
        return redirect()->route('admin.state.show', $city->state);
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function show(City $city)
    {
        return redirect()->to(route('admin.district.index') . '?city_id=' . $city->id);
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(City $city): Factory|View|Application
    {
        $city->load('state');
        return view('admin.pages.city.edit')->with(['city' => $city]);
    }

    /**
     * @role(super_user)
     * @rules(name="required|unique:cities,name,".request()->get('id') , has_district="boolean")
     */
    public function update(Request $request, City $city): RedirectResponse
    {
        $city->update($request->all());
        return History::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(City $city): RedirectResponse
    {
        $city->delete();
        return back();
    }


    public function getModel(): ?string
    {
        return City::class;
    }
}
