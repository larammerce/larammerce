<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article;
use App\Models\Directory;
use App\Utils\CMS\File\ExploreService;
use App\Utils\CMS\SiteMap\Provider as SiteMapProvider;
use App\Utils\Common\History;
use App\Utils\Common\MessageFactory;
use App\Utils\Common\RequestService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class ArticleController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission-system');
    }

    /**
     * @rules(directory_id="exists:directories,id")
     * @role(super_user, seo_master, cms_manager)
     */
    public function index(): Factory|View|Application
    {
        if (request()->has('directory_id')) {
            $directory = Directory::find(request()->get('directory_id'));
            parent::setPageAttribute($directory->id);
            $articles = $directory->articles()->permitted()->with('author.user')->paginate(Article::getPaginationCount());
        } else {
            parent::setPageAttribute();
            $directory = null;
            $articles = Article::with('author.user')->permitted()->paginate(Article::getPaginationCount());
        }
        return view('admin.pages.article.index', compact('articles', 'directory'));
    }

    /**
     * @rules(directory_id="exists:directories,id")
     * @role(super_user, seo_master, cms_manager)
     */
    public function create(Request $request)
    {
        $directory_id = 0;
        if ($request->has('directory_id'))
            $directory_id = $request->get('directory_id');
        else {
            $current = ExploreService::getCurrentDirectory();
            if ($current != null) {
                $directory_id = $current;
            }
        }
        $directory = Directory::find($directory_id);
        return view('admin.pages.article.create', compact('directory'));
    }

    /**
     * @rules(directory_id="required|exists:directories,id", title="required", short_content="required",
     *     full_text="required", image="image|max:2048|dimensions:min_width=".get_image_min_width("blog").
     *     ",ratio=".get_image_ratio("blog"))
     * @role(super_user, seo_master, cms_manager)
     */
    public function store(Request $request)
    {
        $request->merge(["system_user_id" => get_system_user()?->id]);
        $article = Article::create($request->all());

        if ($request->hasFile('image'))
            $article->setImagePath();

        $directory = Directory::find($request->get('directory_id'));
        // attaching article recursively to all parent directories
        $article->attachFileTo($directory);
        $article->createReview();

        SiteMapProvider::save();

        return redirect()->route('admin.article.index');
    }

    /**
     * @role(super_user, seo_master, cms_manager)
     */
    public function show(Article $article): RedirectResponse
    {
        return redirect()->to($article->getFrontUrl());
    }

    /**
     * @role(super_user, seo_master, cms_manager)
     */
    public function edit(Article $article): Factory|View|Application
    {
        $article->load('directory', 'rates', 'tags', 'author');
        return view('admin.pages.article.edit', compact('article'));
    }

    /**
     * @rules(title="required", short_content="required", full_text="required",
     *     image="image|max:2048|dimensions:min_width=".get_image_min_width('blog').
     *     ",ratio=".get_image_ratio('blog'))
     * @role(super_user, seo_master, cms_manager)
     */
    public function update(Request $request, Article $article): RedirectResponse|Response
    {
        $article->update($request->all());

        if ($request->hasFile('image'))
            $article->setImagePath();

        $article->updateReview();

        return History::redirectBack();
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();
        SiteMapProvider::save();
        return History::redirectBack();
    }

    /**
     * @rules(query="required")
     * @role(super_user, seo_master, cms_manager)
     */
    public function search(Request $request)
    {
        return Article::permitted()->search($request->get('query'))->get();
    }

    /**
     * @rules(tags="array", tags.key.*="exists:tags,id")
     * @role(super_user, seo_master, cms_manager)
     */
    public function attachTags(Request $request, Article $article): RedirectResponse
    {
        $article->tags()->detach();
        $article->tags()->attach($request->all());
        return redirect()->route('admin.pages.article.index');
    }

    /**
     * @rules(id="required|exists:tags,id")
     * @role(super_user, seo_master, cms_manager)
     */
    public function attachTag(Request $request, Article $article): RedirectResponse|JsonResponse
    {
        $article->tags()->attach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.article.tag_attached'], 200, compact('article')
            ), 200);
        }
        return redirect()->route('admin.article.index');
    }

    /**
     * @rules(id="required|exists:tags,id")
     * @role(super_user, seo_master, cms_manager)
     */
    public function detachTag(Request $request, Article $article): JsonResponse|RedirectResponse
    {
        $article->tags()->detach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.article.tag_detached'], 200, compact('article')
            ), 200);
        }
        return redirect()->route('admin.article.index');
    }

    /**
     * @role(super_user, seo_master, cms_manager)
     */
    public function removeImage(Article $article): RedirectResponse
    {
        $article->removeImage();
        return back();
    }


    public function getModel(): ?string
    {
        return Article::class;
    }
}
