<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HistoryHelper;
use App\Models\City;
use App\Models\District;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class DistrictController extends BaseController
{
    /**
     * @role(super_user, cms_manager)
     * @rules(city_id="exists:cities,id")
     */
    public function index(): Factory|View|Application
    {
        if (request()->has('city_id')) {
            $city = City::find(request()->get('city_id'));
            parent::setPageAttribute($city->id);
            $districts = $city->districts()->with('city.state')->paginate(District::getPaginationCount());
        } else {
            parent::setPageAttribute();
            $districts = District::with('city.state')->paginate(District::getPaginationCount());
        }
        return view('admin.pages.district.index', compact('districts', 'city'));
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(city_id="required|exists:cities,id")
     */
    public function create(): Factory|View|Application
    {
        $city = City::find(request()->get('city_id'));
        return view('admin.pages.district.create', compact('city'));
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(name="required|unique:districts,name", city_id="required|exists:cities,id")
     */
    public function store(Request $request): RedirectResponse
    {
        $district = District::create($request->all());
        return redirect()->route('admin.city.show', $district->city);
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(District $district): Factory|View|Application
    {
        $district->load('city.state');
        return view('admin.pages.district.edit')->with(['district' => $district]);
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(name="required|unique:districts,name,". request()->get('id'))
     */
    public function update(Request $request, District $district): RedirectResponse
    {
        $district->update($request->all());
        return HistoryHelper::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(District $district): RedirectResponse
    {
        $district->delete();
        return back();
    }


    public function getModel(): ?string
    {
        return District::class;
    }
}
