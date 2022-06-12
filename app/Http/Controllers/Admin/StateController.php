<?php

namespace App\Http\Controllers\Admin;

use App\Models\State;
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
class StateController extends BaseController
{
    /**
     *
     * @role(super_user, cms_manager)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $states = State::paginate(State::getPaginationCount());
        return view('admin.pages.state.index', compact('states'));
    }

    /**
     *
     * @role(super_user, cms_manager)
     */
    public function create(): Factory|View|Application
    {
        return view('admin.pages.state.create');
    }

    /**
     *
     * @role(super_user, cms_manager)
     * @rules(name="required|unique:states,name")
     */
    public function store(Request $request): RedirectResponse
    {
        State::create($request->all());
        return redirect()->route('admin.state.index');
    }

    /**
     *
     * @role(super_user, cms_manager)
     */
    public function show(State $state): RedirectResponse
    {
        return redirect()->to(route('admin.city.index') . '?state_id=' . $state->id);
    }

    /**
     *
     * @role(super_user, cms_manager)
     */
    public function edit(State $state): Factory|View|Application
    {
        return view('admin.pages.state.edit', compact('state'));
    }

    /**
     *
     * @role(super_user, cms_manager)
     * @rules(name="required|unique:states,name,".request()->state?->id)
     */
    public function update(Request $request, State $state): RedirectResponse
    {
        $state->update($request->all());
        return History::redirectBack();
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function destroy(State $state): RedirectResponse
    {
        $state->delete();
        return back();
    }


    public function getModel(): ?string
    {
        return State::class;
    }
}
