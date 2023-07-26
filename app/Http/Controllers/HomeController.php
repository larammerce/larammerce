<?php

namespace App\Http\Controllers;

use App\Enums\Directory\DirectoryType;
use App\Models\Article;
use App\Models\Directory;
use App\Models\ModifiedUrl;
use App\Models\PAttr;
use App\Models\Product;
use App\Models\ProductFilter;
use App\Models\ShortLink;
use App\Services\Product\ProductService;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Stevebauman\Location\Facades\Location;

class HomeController extends Controller
{

    public function main(Request $request)
    {
        $url_path = $request->path();
        $shortened_link = $url_path;
        $url_path = ($url_path != "/" ? "/" : "") . $url_path;
        $url_last_part = last(explode("/", $url_path));
        $requested_host = $request->getHost();
        $shortened_url = config('app.shortened_host');
        if ($requested_host === $shortened_url) {
            try {
                $short_link = ShortLink::findByShortenedLink($shortened_link);
                return $this->visitShortLink($short_link);
            } catch (Exception $e) {
                abort(404);
            }
        } else {
            $url_paths = [$url_path];
            $needs_landing = false;
            if ($url_last_part === "landing") {
                $needs_landing = true;
                $url_paths[] = Str::replaceLast("/landing", "", $url_path);
            }

            /** @var Directory $directory */
            $directory = Directory::whereIn("url_full", $url_paths)->orderBy("has_web_page", "DESC")
                ->orderBy("updated_at", "desc")->first();
            $cart_rows = get_cart();
            if ($directory != null) {
                if (!$directory->is_anonymously_accessible && auth()->guest())
                    return redirect()->guest(route('customer-auth.show-auth',
                        config("auth.default_type.customer")));
                elseif ($directory->content_type == DirectoryType::PRODUCT) {
                    if (str_starts_with($url_last_part, "filter-")) {
                        $filter_identifier = str_replace("filter-", "", $url_last_part);
                        try {
                            $product_filter = ProductFilter::findByIdentifier($filter_identifier);
                            return $this->showProductCustomFilter($directory, $product_filter, $cart_rows);
                        } catch (Exception $e) {
                            abort(404);
                        }
                    }
                    return $this->showProductFilter($directory, $cart_rows, $needs_landing);
                } elseif ($directory->content_type == DirectoryType::BLOG) {
                    return $this->showBlogList($directory);
                } elseif ($directory->has_web_page) {
                    return $this->showWebPage($directory, $cart_rows);
                }
            } else {
                $modified_url = ModifiedUrl::where("url_old", $url_path)->first();
                if (!is_null($modified_url) and isset($modified_url->url_new) and strlen($modified_url->url_new) > 0) {
                    $new_url = $modified_url->url_new;
                    return response()->redirectTo($new_url, 301);
                }
            }
        }
        abort(404);
    }

    private function visitShortLink(ShortLink $short_link): RedirectResponse
    {
        $path = $short_link->link;
        $link_id = $short_link->id;
        $views_count = Cache::get('short-link:views:' . $link_id);
        $location = Location::get();
        $country = $location->countryName;
        if ($views_count != null) {
            $views_count['total_views'] += 1;
            $views_count['countries'][$country] += 1;
        } else {
            $views_count = [
                "total_views" => 1,
                "countries" => [$country => 1],
            ];
        }
        Cache::forever('short-link:views:' . $link_id, $views_count);
        return redirect()->to($path);
    }

    public function showWebPage(Directory $directory, $cart_rows): Factory|Application|View
    {
        $web_page = $directory->webPage;
        return h_view("public." . $web_page->blade_name,
            compact("web_page", "directory", "cart_rows"));
    }

    public function showProduct(Product $product): Factory|Application|View
    {
        if ($product->is_visible or (get_user() !== false and get_user()->is_system_user)) {
            $attributes = PAttr::getProductAttributes($product);
            $blade_name = $product->productStructure->blade_name ?: 'product-single';

            return h_view("public.{$blade_name}", [
                'product' => $product,
            ])->with($attributes);
        }
        abort(404);
    }

    public function showBlog(Article $article): Factory|Application|View
    {
        $blade_name = get_article_single_blade($article->content_type);
        return h_view($blade_name, compact("article"));
    }

    private function showProductFilter(Directory $directory, $cart_rows, bool $needs_landing = false): Factory|Application|View
    {
        $product_ids = $directory->leafProducts()->mainModels()->visible()->pluck("products.id")->toArray();
        $filter_data = ProductService::getFilterData($product_ids);
        if ($needs_landing) {
            $web_page = $directory->webPage;
            if ($web_page != null)
                return h_view("public." . $web_page->blade_name,
                    compact("directory", "web_page", "cart_rows"))->with($filter_data);
        }
        return h_view("public.product-filter",
            compact("directory", "cart_rows"))->with($filter_data);
    }

    private function showProductCustomFilter(Directory $directory, ProductFilter $product_filter, $cart_rows): Factory|Application|View
    {
        $product_ids = $product_filter->getQuery()->mainModels()->visible()->pluck("products.id")->toArray();
        $filter_data = ProductService::getFilterData($product_ids);
        $web_page = $directory->webPage;
        return h_view("public." . $web_page->blade_name,
            compact("product_filter", "web_page", "cart_rows"))->with($filter_data);
    }

    private function showBlogList($directory): Factory|Application|View
    {
        $articles = $directory->getPaginatedBlog();
        $blade_name = get_article_list_blade($directory);
        return h_view($blade_name, compact("articles", "directory"));
    }

    /**
     * @rules(query="required")
     */
    public function search(): RedirectResponse|Factory|Application|View
    {
        $query = request("query");
        $product_ids = Product::search($query)->pluck("id")->toArray();

        if (count(is_countable($product_ids) ? $product_ids : []) === 1) {
            $found_product = Product::find($product_ids[0]);
            return redirect()->to($found_product->getFrontUrl());
        }

        $directories = Directory::WhereHas('products', function ($q) use ($product_ids, $query) {
            $q->whereIn('id', $product_ids);
            foreach (Directory::getSearchableFields() as $field_index => $searchable_field) {
                $q->where($searchable_field, 'LIKE', '%' . $query . '%');
            }
        })->get();

        $filter_data = ProductService::getFilterData($product_ids);
        return h_view("public.product-filter",
            compact("directories", "query"))->with($filter_data);
    }
}
