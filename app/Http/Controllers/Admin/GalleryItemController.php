<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HistoryHelper;
use App\Models\Gallery;
use App\Models\GalleryItem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class GalleryItemController extends BaseController
{
    /**
     * @role(super_user, cms_manager)
     * @rules(gallery_id="required|exists:galleries,id")
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute(request()->get("gallery_id"));
        $gallery = Gallery::find(request('gallery_id'));
        $gallery_items = $gallery->items()->paginate(GalleryItem::getPaginationCount());
        return view('admin.pages.gallery-item.index', compact('gallery_items', 'gallery'));
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(gallery_id="required|exists:galleries,id")
     */
    public function create(): Factory|View|Application
    {
        $gallery = Gallery::find(request('gallery_id'));
        return view('admin.pages.gallery-item.create', compact('gallery'));
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(gallery_id="required|exists:galleries,id", is_active="required|bool")
     */
    public function store(Request $request): RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $min_priority = GalleryItem::min('priority');
        $request_data = $request->all();
        $request_data['priority'] = $min_priority - 1;
        $gallery_item = GalleryItem::create($request_data);

        if ($request->hasFile('image'))
            $gallery_item->setImagePath();

        return redirect()->route('admin.gallery.show', $gallery_item->gallery);
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function show(GalleryItem $gallery_item): Response|Application|ResponseFactory
    {
        return response('gallery item show page');
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function edit(GalleryItem $gallery_item): Factory|View|Application
    {
        $gallery_item->load('gallery');
        return view('admin.pages.gallery-item.edit')->with([
            'gallery_item' => $gallery_item,
            'neededPriorityToShow' => (GalleryItem::min('priority') - 1)]);
    }

    /**
     * @role(super_user, cms_manager)
     * @rules(is_active="required|bool")
     */
    public function update(Request $request, GalleryItem $gallery_item): RedirectResponse
    {
        $gallery_item->update($request->all());

        $gallery_item->save();

        if ($request->hasFile('image'))
            $gallery_item->setImagePath();

        return HistoryHelper::redirectBack();
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function destroy(GalleryItem $gallery_item): RedirectResponse
    {
        $gallery_item->delete();
        return back();
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function removeImage(GalleryItem $gallery_item): RedirectResponse
    {
        $gallery_item->removeImage();
        return back();
    }


    public function getModel(): ?string
    {
        return GalleryItem::class;
    }
}
