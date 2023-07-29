<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FormHelper;
use App\Helpers\HistoryHelper;
use App\Models\Gallery;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class GalleryController extends BaseController
{
    /**
     * @role(super_user, cms_manager)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $galleries = Gallery::with('items')->paginate(Gallery::getPaginationCount());
        return view('admin.pages.gallery.index', compact('galleries'));
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function create(): Factory|View|Application
    {
        return view('admin.pages.gallery.create');
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(identifier="required|unique:galleries")
     */
    public function store(): RedirectResponse
    {
        $fields = json_decode(request('fields'));
        if ($fields != null)
            FormHelper::convertFormInputToKeys($fields);
        Gallery::create(request()->all());
        return redirect()->route('admin.gallery.index');
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function show(Gallery $gallery): RedirectResponse
    {
        return redirect()->to(route('admin.gallery-item.index') . '?gallery_id=' . $gallery->id);
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(Gallery $gallery): Factory|View|Application
    {
        $gallery->load('items');
        return view('admin.pages.gallery.edit')->with(['gallery' => $gallery]);
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(identifier="required|unique:galleries,identifier,".request()->get('id'))
     */
    public function update(Gallery $gallery): RedirectResponse
    {
        $fields = json_decode(request('fields'));
        if ($fields != null)
            FormHelper::convertFormInputToKeys($fields);
        $gallery->update(request()->all());
        return HistoryHelper::redirectBack();
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function destroy(Gallery $gallery): RedirectResponse
    {
        $gallery->delete();
        return back();
    }


    public function getModel(): ?string
    {
        return Gallery::class;
    }
}
