<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 11/13/18
 * Time: 4:35 PM
 */

namespace App\Models;

use App\Services\Product\ProductService;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 *
 * @property integer id
 * @property string identifier
 * @property string title
 * @property string data
 * @property integer product_query_id
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * @property ProductQuery productQuery
 *
 * @method static ProductFilter find(integer $id)
 *
 * Class ProductFilter
 * @package App\Models
 */
class ProductFilter extends BaseModel
{
    protected $table = "product_filters";
    protected $attributes = [
        'data' => '[]'
    ];
    protected $fillable = [
        'identifier', 'title', 'product_query_id', 'data',

        //these are not table fields, these are form sections that role permission system works with
        'filter_data'
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'title', 'created_at'];
    protected static array $ROLE_PROPERTY_ACCESS = [
        "super_user" => ["*"],
        "cms_manager" => [
            'data', 'product_query_id', 'query_data'
        ]
    ];

    protected static array $SEARCHABLE_FIELDS = [
        'identifier', 'title'
    ];

    public function setDataAttribute(array $data)
    {
        if (isset($data["ps_values"]))
            $data["ps_values"] = array_values($data["ps_values"]);
        $this->attributes["data"] = $data ? json_encode($data) : '[]';
    }

    public function productQuery(): BelongsTo
    {
        return $this->belongsTo(ProductQuery::class, "product_query_id", "id");
    }

    public function discountGroups(): BelongsToMany
    {
        return $this->belongsToMany(DiscountGroup::class, "discount_group_product_filter", "product_filter_id", "discount_group_id");
    }

    public function getSearchUrl(): string
    {
        return '';
    }

    public static function findByIdentifier(string $identifier): ProductFilter
    {
        return ProductFilter::where("identifier", $identifier)->firstOrFail();
    }

    public function getData()
    {
        return json_decode($this->data, true);
    }

    public function getDirectoryIds(): array
    {
        $data = $this->getData();
        if (isset($data["directories"]))
            return $data["directories"];
        return [];
    }

    public function getDirectories()
    {
        return Directory::whereIn("id", $this->getDirectoryIds())->get();
    }

    public function getPSValueIds(): array
    {
        $data = $this->getData();
        if (isset($data["ps_values"]))
            return $data["ps_values"];
        return [];
    }

    public function getPSValueIdsPlain(): array
    {
        $result = [];

        foreach ($this->getPSValueIds() as $psv_ids) {
            if (is_array($psv_ids)) {
                $result = array_merge($result, $psv_ids);
            } else {
                $result[] = $psv_ids;
            }
        }

        return $result;
    }

    public function getPSValues()
    {
        return PStructureAttrValue::whereIn("id", $this->getPSValueIdsPlain());
    }

    public function getQuery()
    {
        $directory_ids = $this->getDirectoryIds();
        $psv_ids = $this->getPSValueIds();
        $products_query = Product::query();
        if (count(is_countable($directory_ids) ? $directory_ids : []) > 0) {
            $products_query = $products_query->whereHas("directories", function ($query) use ($directory_ids) {
                $query->whereIn("id", $directory_ids);
            });
        }

        if (count(is_countable($psv_ids) ? $psv_ids : []) > 0) {
            foreach ($psv_ids as $value) {
                if (is_array($value)) {
                    $products_query = $products_query->whereHas("pAttributes", function ($query) use ($value) {
                        $query->whereIn("p_structure_attr_value_id", $value);
                    });
                } else {
                    $products_query = $products_query->whereHas("pAttributes", function ($query) use ($value) {
                        $query->where("p_structure_attr_value_id", $value);
                    });
                }
            }
        }

        if ($this->productQuery !== null) {
            $products_query = $this->productQuery->getQuery($products_query);
        }

        return $products_query;
    }

    public function getProductIds(): array
    {
        return $this->getQuery()->select("products.id")->pluck("id")->toArray();
    }

    public function getProductsQueryBuilder(): Builder
    {
        return Product::mainModels()->visible()->whereIn("id", $this->getProductIds());
    }

    public function getProducts()
    {
        return Product::mainModels()->visible()->whereIn("id", $this->getProductIds())->get();
    }

    public function getFilterData(): array
    {
        return ProductService::getFilterData($this->getProductIds());
    }

    public function attachToDiscountGroup(DiscountGroup $discount_group)
    {
        $this->getProductsQueryBuilder()->update([
            "discount_group_id" => $discount_group->id
        ]);
        $discount_group->filters()->syncWithoutDetaching([$this->id]);
    }

    public function detachFromDiscountGroup(DiscountGroup $discount_group)
    {
        $this->getProductsQueryBuilder()->update([
            "discount_group_id" => null
        ]);
        $discount_group->filters()->detach($this->id);
    }
}
