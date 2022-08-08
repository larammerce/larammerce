<?php

namespace App\Http\Controllers\Admin;

use App\Models\Directory;
use App\Models\Product;
use App\Models\PAttr;
use App\Models\ProductFilter;
use App\Models\PStructure;
use App\Models\PStructureAttrKey;
use App\Utils\CMS\File\ExploreService;
use App\Utils\CMS\FormService;
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


/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class ProductController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission-system');
    }

    /**
     * @role(super_user, stock_manager, seo_master, cms_manager)
     * @rules(directory_id="exists:directories,id", product_filter_id="exists:product_filters,id")
     */
    public function index()
    {
        if (request()->has('directory_id')) {
            $directory = Directory::permitted()->where("id", request()->get('directory_id'))->first();
            if ($directory != null) {
                parent::setPageAttribute($directory->id);
                $products = $directory->leafProducts()->with('productStructure', 'images', 'rates')
                    ->paginate(Product::getPaginationCount());
            } else {
                abort(403);
            }
            return view('admin.pages.product.index', compact('products', 'directory'));
        } else if (request()->has("product_filter_id")) {
            $filter = ProductFilter::find(request()->get("product_filter_id"));
            parent::setPageAttribute($filter->id);
            $products = $filter->getProductsQueryBuilder()->paginate(Product::getPaginationCount());
            return view('admin.pages.product.index', compact('products', 'filter'));
        } else {
            parent::setPageAttribute();
            $products = Product::with('productStructure', 'images', 'rates')->permitted()
                ->paginate(Product::getPaginationCount());
            return view('admin.pages.product.index', compact('products'));
        }
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(directory_id="exists:directories,id")
     */
    public function create(Request $request): Factory|View|Application
    {
        $directoryId = 0;
        if ($request->has('directory_id'))
            $directoryId = $request->get('directory_id');
        else {
            $current = ExploreService::getCurrentDirectory();
            if ($current != null) {
                $directoryId = $current;
            }
        }
        $directory = Directory::find($directoryId);
        $p_structures = PStructure::all();
        return view('admin.pages.product.create', compact('p_structures', 'directory'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(title="required", directory_id="required|exists:directories,id",
     *     p_structure_id="required|exists:p_structures,id",
     *     code="unique:products,code",
     *     is_package="boolean")
     */
    public function store(Request $request): RedirectResponse
    {
        $product = Product::create($request->all());
        $directory = Directory::find($request->get('directory_id'));
        $product->attachFileTo($directory);
        $product->createReview();
        if ($request->has("is_package") and $request->get("is_package") == 1)
            $product->productPackage()->create([]);
        SiteMapProvider::save();
        return redirect()->route('admin.product.edit', $product);
    }

    /**
     * @role(super_user, stock_manager, seo_master, cms_manager, acc_manager)
     */
    public function show(Product $product): RedirectResponse
    {
        return redirect()->to($product->getFrontUrl());
    }

    /**
     * @role(super_user, stock_manager, seo_master, cms_manager, acc_manager)
     */
    public function edit(Product $product): Factory|View|Application
    {
        $relations = ['directory', 'productStructure', 'images', 'pAttributes', 'tags'];
        if ($product->is_package)
            $relations[] = 'productPackage';
        $product->load($relations);
        $p_structures = PStructure::all();
        return view('admin.pages.product.edit')->with(compact("p_structures", "product"));
    }

    /**
     * @rules(title="required",
     *     p_structure_id="required|exists:p_structures,id",
     *     code="required|unique:products,code,".request('id'))
     * @role(super_user, stock_manager, seo_master, cms_manager, acc_manager)
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($request->all());
        $product->extra_properties = FormService::getEncodedFormProperties($request);
        $product->save();
        $product->updateReview();
        return History::redirectBack();
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        SiteMapProvider::save();
        return back();
    }

    /**
     * @role(super_user, stock_manager, seo_master, cms_manager, acc_manager)
     * @rules(query="required")
     */
    public function search(Request $request)
    {
        return Product::permitted()->search($request->get('query'))->get();
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(id="required|exists:p_structure_attr_values,id")
     */

    public function attachAttribute(Request $request, Product $product, PStructureAttrKey $key): JsonResponse|RedirectResponse
    {
        PAttr::create([
            "product_id" => $product->id,
            "p_structure_attr_key_id" => $key->id,
            "p_structure_attr_value_id" => $request->get('id'),
        ]);
        $product->attributes_content = $request->get('data_text');
        $product->save();

        if ($key->is_sortable) {
            $product->buildStructureSortScore($key);
        }


        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.product.attribute_attached'], 200, compact('product')
            ), 200);
        }
        return redirect()->route('admin.product.index');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(id="required|exists:p_structure_attr_values,id")
     */
    public function detachAttribute(Request $request, Product $product, PStructureAttrKey $key): JsonResponse|RedirectResponse
    {
        $product->pAttributes()
            ->where('p_structure_attr_key_id', '=', $key->id)
            ->where('p_structure_attr_value_id', '=', $request->get('id'))
            ->delete();
        $product->attributes_content = $request->get('data_text');
        $product->save();

        if ($key->is_sortable) {
            $product->buildStructureSortScore($key);
        }

        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.product.attribute_detached'], 200, compact('product')
            ), 200);
        }
        return redirect()->route('admin.product.index');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(colors="array", colors.*="exists:colors,id")
     */
    public function attachColors(Request $request, Product $product): RedirectResponse
    {
        $product->colors()->detach();
        $product->colors()->attach($request->get('colors'));
        return redirect()->route('admin.pages.product.index');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(id="required|exists:colors,id")
     */
    public function attachColor(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $product->colors()->attach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.product.color_attached'], 200, compact('product')
            ), 200);
        }
        return redirect()->route('admin.product.index');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(id="required|exists:colors,id")
     */
    public function detachColor(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $product->colors()->detach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.product.color_detached'], 200, compact('product')
            ), 200);
        }
        return redirect()->route('admin.product.index');
    }


    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(tags="array", tags.key.*="exists:tags,id")
     */
    public function attachTags(Request $request, Product $product): RedirectResponse
    {
        $product->tags()->detach();
        $product->tags()->attach($request->all());
        return redirect()->route('admin.pages.product.index');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(id="required|exists:tags,id")
     */
    public function attachTag(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $product->tags()->attach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.product.tag_attached'], 200, compact('product')
            ), 200);
        }
        return redirect()->route('admin.product.index');
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(id="required|exists:tags,id")
     */
    public function detachTag(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $product->tags()->detach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.product.tag_detached'], 200, compact('product')
            ), 200);
        }
        return redirect()->route('admin.product.index');
    }


    /**
     * Attach Badge to the product.
     *
     * @role(super_user, cms_manager, acc_manager)
     * @rules(id="required|exists:badges,id")
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function attachBadge(Request $request, Product $product)
    {
        $product->badges()->attach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.product.badge_attached'], 200, compact('product')
            ), 200);
        }
        return redirect()->route('admin.product.index');
    }

    /**
     * Detach Badge from the product.
     *
     * @role(super_user, cms_manager, acc_manager)
     * @rules(id="required|exists:badges,id")
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function detachBadge(Request $request, Product $product)
    {
        $product->badges()->detach($request->get('id'));
        if (RequestService::isRequestAjax()) {
            return response()->json(MessageFactory::create(
                ['messages.product.badge_detached'], 200, compact('product')
            ), 200);
        }
        return redirect()->route('admin.product.index');
    }


    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function cloneModel(Request $request, Product $product): RedirectResponse
    {
        $new_product = $product->cloneFile();
        $new_product->directory->attachLeafFiles($new_product->id);
        return redirect()->route('admin.product.edit', $new_product);
    }

    public function models(Request $request, Product $product): Factory|View|Application
    {
        $scope = "scope_" . $product->model_id;
        parent::setPageAttribute($scope);
        $products = Product::models($product, false)
            ->with('productStructure', 'images', 'rates')
            ->permitted()
            ->paginate(Product::getPaginationCount());
        return view('admin.pages.product.index', compact('products', 'scope'));
    }

    public function import(): Factory|View|Application
    {
        return view('admin.pages.excel.import');
    }

    public function getModel(): ?string
    {
        return Product::class;
    }
}
