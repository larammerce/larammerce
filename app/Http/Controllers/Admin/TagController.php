<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Utils\Common\History;
use App\Utils\Common\MessageFactory;
use App\Utils\Common\RequestService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class TagController extends BaseController
{
    /**
     *
     * @role(super_user, cms_manager, seo_master)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $tags = Tag::paginate(Tag::getPaginationCount());
        return view('admin.pages.tag.index', compact('tags'));
    }

    /**
     *
     * @role(super_user, cms_manager, seo_master)
     */
    public function create(): Factory|View|Application
    {
        return view('admin.pages.tag.create');
    }

    /**
     *
     * @role(super_user, cms_manager, seo_master)
     * @rules(name="required|unique:tags")
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $item = Tag::create($request->all());
        if (RequestService::isRequestAjax($request)) {
            return response()->json(MessageFactory::create(
                ['messages.tag.tag_added'], 200, compact('item')
            ), 200);
        }
        return redirect()->route('admin.tag.index');
    }

    /**
     *
     * @role(super_user, cms_manager, seo_master)
     */
    public function edit(Tag $tag): Factory|View|Application
    {
        return view('admin.pages.tag.edit', compact('tag'));
    }

    /**
     *
     * @role(super_user, cms_manager, seo_master)
     * @rules(name="required|unique:tags,name," . request()->get('id'))
     */
    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $tag->update($request->all());
        return History::redirectBack();
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();
        return redirect()->route('admin.tag.index');
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     * @rules(query="required")
     */
    public function query(Request $request): \Response|Response|JsonResponse|Application|ResponseFactory
    {
        $collection = Tag::where('name', 'like', '%' . $request->input('query') . '%')->get();
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                [], 200, compact('collection')
            ), 200);
        }
        return response(json_encode($collection));
    }


    public function getModel(): ?string
    {
        return Tag::class;
    }
}
