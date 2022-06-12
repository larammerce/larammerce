<?php

namespace App\Http\Controllers\Admin;

use App\Models\Color;
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
class ColorController extends BaseController
{
    /**
     * @role(super_user, cms_manager)
     */
    public function index(): View|Factory|JsonResponse|Application
    {
        parent::setPageAttribute();

        if (RequestService::isRequestAjax())
            return response()->json(Color::all());

        $colors = Color::paginate(Color::getPaginationCount());
        return view('admin.pages.color.index', compact('colors'));
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function create(): Factory|View|Application
    {
        return view('admin.pages.color.create');
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(name="required|unique:colors", hex_code="required|unique:colors")
     */
    public function store(Request $request): RedirectResponse
    {
        $color = Color::create($request->all());
        if ($request->hasFile('image'))
            $color->setImagePath();
        return redirect()->route('admin.color.index');
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function show(Color $color)
    {
        //TODO : we must make a view page for colors
        return response()->make($color->name);
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(Color $color): Factory|View|Application
    {
        return view('admin.pages.color.edit', compact('color'));
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(name="required|unique:colors,name,".request()->color?->id, hex_code="required|unique:colors,hex_code,".request()->color?->id)
     */
    public function update(Request $request, Color $color): RedirectResponse
    {
        $color->update($request->all());
        if ($request->hasFile('image'))
            $color->setImagePath();
        return History::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(Color $color): RedirectResponse
    {
        $color->delete();
        return back();
    }

    /**
     * @role(super_user, seo_master, cms_manager)
     */
    public function removeImage(Color $color): RedirectResponse
    {
        $color->removeImage();
        return back();
    }


    public function getModel(): ?string
    {
        return Color::class;
    }
}
