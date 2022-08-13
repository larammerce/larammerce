<?php

namespace App\Http\Controllers\Admin;

use App\Models\WebPage;
use App\Utils\CMS\Template\Directives;
use App\Utils\CMS\Template\TemplateModel;
use App\Utils\CMS\Template\TemplateService;
use App\Utils\Common\History;
use App\Utils\Common\MessageFactory;
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
class WebPageController extends BaseController
{
    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $webPages = WebPage::with('directory', 'tags')->paginate(WebPage::getPaginationCount());
        return view('admin.pages.web-page.index', compact('webPages'));
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function create(): Factory|View|Application
    {
        return view('admin.pages.web-page.create');
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     * @rules(directory_id="required|exists:directories,id",
     *     image="image|max:2048|dimensions:min_width=".get_image_min_width('web_page').",ratio=".get_image_ratio('web_page'))
     */
    public function store(Request $request): RedirectResponse
    {
        $webPage = WebPage::create($request->all());
        $webPage->createReview();
        return redirect()->route('admin.pages.web-page.index');
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function show(WebPage $web_page): RedirectResponse
    {
        return redirect()->to($web_page->directory->url_full);
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function edit(WebPage $web_page): Factory|View|Application
    {
        $web_page->load('directory', 'tags');
        if (strlen($web_page->blade_name) !== 0) {
            return view('admin.pages.web-page.edit')->with(compact("web_page"));
        }
        return view('admin.pages.web-page.initial')->with(compact("web_page"));
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     * @rules(image="image|max:2048|dimensions:min_width=".get_image_min_width('web_page').",ratio=".get_image_ratio('web_page'))
     */
    public function update(Request $request, WebPage $web_page): RedirectResponse
    {
        $web_page->update($request->all());
        if (isset($web_page->blade_name) and $web_page->blade_name != null) {
            $template = new TemplateModel($web_page->blade_name,
                TemplateService::getBladePath($web_page->blade_name));
            $galleryTags = $template->selectDirectiveTags(Directives::GALLERY);
            foreach ($galleryTags as $galleryTag) {
                $galleryName = $galleryTag->attr[Directives::GALLERY];
                if (isset($galleryTag->attr[Directives::UNSHARED]) and
                    $galleryTag->attr[Directives::UNSHARED] == "true")
                    $template->createGalleryModel($galleryTag,
                        $galleryName . "_" . $web_page->directory->id);
            }
        }

        if ($request->hasFile('image'))
            $web_page->setImagePath();

        $web_page->updateReview();

        return History::redirectBack();
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function destroy(WebPage $web_page): RedirectResponse
    {
        $web_page->directory->update(['has_web_page' => false]);
        return back();
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     * @rules(tags="array", tags.*="exists:tags,id")
     */
    public function attachTags(Request $request, WebPage $web_page): RedirectResponse
    {
        $web_page->tags()->detach();
        $web_page->tags()->attach($request->get('tags'));
        return redirect()->route('admin.pages.web-page.index');
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     * @rules(id="required|exists:tags,id")
     */
    public function attachTag(Request $request, WebPage $web_page): JsonResponse|RedirectResponse
    {
        $web_page->tags()->attach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.web_page.tag_attached'], 200, compact('web_page')
            ), 200);
        }
        return redirect()->route('admin.web_page.index');
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     * @rules(id="required|exists:tags,id")
     */
    public function detachTag(Request $request, WebPage $web_page): JsonResponse|RedirectResponse
    {
        $web_page->tags()->detach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.web_page.tag_detached'], 200, compact('web_page')
            ), 200);
        }
        return redirect()->route('admin.web_page.index');
    }

    /**
     * @role(super_user, cms_manager, seo_master)
     */
    public function removeImage(WebPage $web_page): RedirectResponse
    {
        $web_page->removeImage();
        return back();
    }


    public function getModel(): ?string
    {
        return WebPage::class;
    }
}
