<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Directory\DirectoryType;
use App\Jobs\ActionDirectoryChildrenBadges;
use App\Jobs\UpdateProductsSpecialPrice;
use App\Models\Directory;
use App\Models\Product;
use App\Utils\CMS\File\ExploreService;
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
class DirectoryController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission-system');
    }

    /**
     * @role(super_user, cms_manager, stock_manager, acc_manager)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        ExploreService::setCurrentDirectory(0);
        $directories = Directory::roots()->permitted()->paginate(Directory::getPaginationCount());
        return view('admin.pages.directory.index', compact('directories'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function create(): Factory|View|Application
    {
        $directory = null;
        if (request()->has('directory_id'))
            $directory = Directory::find(request()->get('directory_id'));
        return view('admin.pages.directory.create', compact('directory'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(title='required', is_internal_link='boolean', priority='required|numeric', has_web_page='boolean')
     */
    public function store(Request $request): RedirectResponse
    {
        $directory = Directory::create($request->except(["is_location_limited"]));
        $directory->setUrlFull();

        if ($request->hasFile('image'))
            $directory->setImagePath();

        $user_role_ids = get_system_user()?->roles()->pluck('id')->toArray();
        $directory->systemRoles()->sync($user_role_ids);

        return redirect()->route('admin.directory.edit', $directory);
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function show(Directory $directory): View|Factory|Application|RedirectResponse
    {
        ExploreService::setCurrentDirectory($directory->id);
        parent::setPageAttribute($directory->id);
        if ($directory->products()->count() > 0) {
            return redirect()->to(route('admin.product.index') . '?directory_id=' . $directory->id);
        } else if ($directory->articles()->count() > 0) {
            return redirect()->to(route('admin.article.index') . '?directory_id=' . $directory->id);
        }
        return view('admin.pages.directory.index')->with(
            [
                'directory' => $directory,
                'directories' => $directory->directories()->permitted()->paginate(Directory::getPaginationCount()),
                'parentDirectory' => $directory->parentDirectory,
            ]
        );
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function edit(Directory $directory): Factory|View|Application
    {
        $directory->load('parentDirectory', 'systemRoles');
        return view('admin.pages.directory.edit', compact('directory'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(title='required', is_internal_link='boolean' , is_anonymously_accessible='boolean', priority='required|numeric',
     *     image="image|max:2048|dimensions:min_width=".get_image_min_width('directory').",ratio=".get_image_ratio('directory'),
     *     directory_id='exists:directories,id', has_web_page='boolean', notice="max:255")
     */
    public function update(Request $request, Directory $directory): RedirectResponse
    {
        $leaf_directory_ids = [];
        if ($request->has("notice") and $request->get("notice") !== $directory->notice) {
            $leaf_directory_ids = $directory->leafProducts()->pluck("id")->toArray();
            Product::whereIn("id", $leaf_directory_ids)
                ->update($request->only("notice"));
        }

        if ($request->has("inaccessibility_type") and $request->get("inaccessibility_type") !== $directory->inaccessibility_type) {
            $leaf_directory_ids = count($leaf_directory_ids) === 0 ? $directory->leafProducts()->pluck("id")->toArray() : $leaf_directory_ids;
            Product::whereIn("id", $leaf_directory_ids)
                ->update($request->only("inaccessibility_type"));
        }


        $directory->update($request->except(["is_location_limited"]));
        $directory->setUrlFull();
        $directory->updateChildrenUrlFull();

        if ($request->hasFile('image'))
            $directory->setImagePath();

        return History::redirectBack();
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function destroy(Directory $directory): RedirectResponse
    {
        $parentDirectory = $directory->parentDirectory;
        $directory->delete();

        if ($parentDirectory)
            return redirect()->route('admin.directory.show', $parentDirectory);
        else
            return redirect()->route('admin.directory.index');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(query="required")
     */
    public function search(Request $request)
    {
        return Directory::permitted()->search($request->get('query'))->get();
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(id="required|exists:system_roles,id")
     */
    public function attachRole(Request $request, Directory $directory): JsonResponse|RedirectResponse
    {
        $directory->systemRoles()->attach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.directory.role_attached'], 200, compact('directory')
            ), 200);
        }
        return redirect()->route('admin.directory.index');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(id="required|exists:system_roles,id")
     */
    public function detachRole(Request $request, Directory $directory): JsonResponse|RedirectResponse
    {
        $directory->systemRoles()->detach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.directory.role_detached'], 200, compact('directory')
            ), 200);
        }
        return redirect()->route('admin.directory.index');
    }


    /**
     * Attach Badge to the directory.
     *
     * @role(super_user, cms_manager)
     * @rules(id="required|exists:badges,id")
     * @param Request $request
     * @param Directory $directory
     * @return RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function attachBadge(Request $request, Directory $directory)
    {
        $badge_id = $request->get('id');
        $directory->badges()->attach($badge_id);
        $job = new ActionDirectoryChildrenBadges($directory, $badge_id, ActionDirectoryChildrenBadges::ATTACH);
        dispatch($job);

        if (RequestService::isRequestAjax()) {
            if ($directory->content_type == \App\Enums\Directory\DirectoryType::PRODUCT) {
                $action_success_message = "messages.directory.badge_attached_to_products";
            } elseif ($directory->content_type == \App\Enums\Directory\DirectoryType::BLOG) {
                $action_success_message = "messages.directory.badge_attached_to_articles";
            } else {
                $action_success_message = "messages.directory.badge_attached";
            }
            return response()->json(MessageFactory::create(
                [$action_success_message], 200, compact('directory')
            ), 200);
        }
        return redirect()->route('admin.directory.index');
    }

    /**
     * Detach Badge from the directory.
     *
     * @role(super_user, cms_manager)
     * @rules(id="required|exists:badges,id")
     * @param Request $request
     * @param Directory $directory
     * @return RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function detachBadge(Request $request, Directory $directory)
    {
        $badge_id = $request->get('id');
        $directory->badges()->detach($badge_id);
        $job = new ActionDirectoryChildrenBadges($directory, $badge_id, ActionDirectoryChildrenBadges::DETACH);
        dispatch($job);

        if (RequestService::isRequestAjax()) {
            if ($directory->content_type == \App\Enums\Directory\DirectoryType::PRODUCT) {
                $action_success_message = "messages.directory.badge_detached_from_products";
            } elseif ($directory->content_type == \App\Enums\Directory\DirectoryType::BLOG) {
                $action_success_message = "messages.directory.badge_detached_from_articles";
            } else {
                $action_success_message = "messages.directory.badge_detached";
            }
            return response()->json(MessageFactory::create(
                [$action_success_message], 200, compact('directory')
            ), 200);
        }

        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.directory.badge_detached'], 200, compact('directory')
            ), 200);
        }
        return redirect()->route('admin.directory.index');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(id="exists:directories,id")
     */
    public function appliances(Request $request): Factory|View|Application
    {
        $config = [
            "article_management" => false,
            "product_management" => false,
            "directory_management" => true
        ];
        $directory = null;

        if ($request->has('id')) {
            $directory = Directory::find($request->get('id'));

            if ($directory->directories()->count() == 0)
                if ($directory->content_type == DirectoryType::BLOG)
                    $config["article_management"] = true;
                else if ($directory->content_type == DirectoryType::PRODUCT)
                    $config["product_management"] = true;

            if ($directory->articles()->count() > 0)
                $config["directory_management"] = false;
            if ($directory->products()->count() > 0)
                $config["directory_management"] = false;
            if ($directory->url_part == "")
                $config["directory_management"] = false;

        }

        return view('admin.pages.directory.appliances', compact('config', 'directory'));
    }

    /**
     * @role(super_user, seo_master, cms_manager, acc_manager)
     */
    public function removeImage(Directory $directory): RedirectResponse
    {
        $directory->removeImage();
        return back();
    }

    /**
     * @role(super_user, seo_master, cms_manager, acc_manager)
     */
    public function showLinkProduct(Directory $directory): Factory|View|Application
    {
        return view('admin.pages.directory.link-product', compact('directory'));
    }


    /**
     * @rules(product_id="required|exists:products,id")
     * @role(super_user, seo_master, cms_manager, acc_manager)
     */
    public function doLinkProduct(Request $request, Directory $directory): RedirectResponse
    {
        $directory->attachLeafFiles($request->get('product_id'));
        return redirect()->route('admin.directory.show', $directory);
    }

    /**
     * @role(super_user, seo_master, cms_manager, acc_manager)
     */
    public function unlinkProduct(Directory $directory, Product $product): RedirectResponse
    {
        $directory->detachLeafFiles($product->id);
        return redirect()->route('admin.directory.show', $directory);
    }

    /**
     * @role(super_user, seo_master, cms_manager, acc_manager)
     */
    public function editSpecialPrice(Directory $directory): Factory|View|Application
    {
        return view("admin.pages.directory.special-price.edit", compact("directory"));
    }

    /**
     * @rules(descent_percentage="required|numeric|between:1,100")
     * @role(super_user, seo_master, cms_manager, acc_manager)
     */
    public function updateSpecialPrice(Directory $directory): RedirectResponse
    {
        $job = new UpdateProductsSpecialPrice($directory, request()->get("descent_percentage"));
        dispatch($job);
        return History::redirectBack();
    }

    /**
     * @role(super_user, seo_master, cms_manager, acc_manager)
     */
    public function destroySpecialPrice(Directory $directory): RedirectResponse
    {
        $directory->leafProducts()->chunk(100,
            function ($products) {
                foreach ($products as $product) {
                    $product->update([
                        "latest_special_price" => 0,
                        "has_discount" => false
                    ]);
                    $product->specialPrices()->delete();
                }
            });
        return redirect()->route("admin.directory.edit", $directory);
    }


    public function getModel(): ?string
    {
        return Directory::class;
    }
}
