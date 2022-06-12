<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 11/13/18
 * Time: 4:35 PM
 */

namespace App\Models;

use DateTime;
use Illuminate\Support\Facades\DB;

/**
 *
 * @property integer id
 * @property string identifier
 * @property string title
 * @property string data
 * @property int skip_count
 * @property int take_count
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * @method static ProductQuery find(integer $id)
 *
 * Class ProductQuery
 * @package App\Models
 */
class ProductQuery extends BaseModel
{
    protected $table = "product_queries";
    protected $attributes = [
        'data' => '[]'
    ];
    protected $fillable = [
        'identifier', 'title', 'data', 'skip_count', 'take_count',

        //these are not table fields, these are form sections that role permission system works with
        'query_data'
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'title', 'created_at'];
    protected static array $ROLE_PROPERTY_ACCESS = [
        "super_user" => ["*"],
        "cms_manager" => [
            'data', 'skip_count', 'take_count', 'query_data'
        ]
    ];

    /**
     * @param string $identifier
     * @return ProductQuery
     */
    public static function findByIdentifier(string $identifier): ProductQuery
    {
        return ProductQuery::where("identifier", $identifier)->firstOrFail();
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }

    /**
     * @param array $attributes
     * @param array $options
     * @return bool
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        $data = [];
        if (key_exists("scopes", $attributes)) {
            foreach ($attributes["scopes"] as $scope) {
                $flag = true;
                foreach ($scope as $key => $value) {
                    if ($value === "none") {
                        $flag = false;
                        break;
                    }
                }
                if ($flag) {
                    $data[] = $scope;
                }
            }
        }

        $attributes["data"] = json_encode($data);
        return parent::update($attributes, $options);
    }

    public function getData()
    {
        return json_decode($this->data);
    }

    public function getQuery($base_query = null)
    {
        $products_query = DB::table("products");
        $data = $this->getData();
        if ($base_query !== null)
            $products_query = $base_query;

        foreach ($data as $index => $scope) {
            switch ($scope->type) {
                case "sort" :
                    $products_query = $products_query->orderBy("products.{$scope->field}", $scope->option);
                    break;
                case "condition" :
                    switch ($scope->option) {
                        case "eq" :
                            $products_query = $products_query->whereRaw(DB::raw("products.{$scope->field} = {$scope->value}"));
                            break;
                        case "gt" :
                            $products_query = $products_query->whereRaw(DB::raw("products.{$scope->field} > {$scope->value}"));
                            break;
                        case "lt":
                            $products_query = $products_query->whereRaw(DB::raw("products.{$scope->field} < {$scope->value}"));
                            break;
                        case "in" :
                            $list = [];
                            foreach (explode(',', $scope->value) as $item) {
                                $item = trim($item);
                                if (strlen($item) > 0)
                                    $list [] = $item;
                            }
                            $products_query = $products_query->whereIn("products.{$scope->field}", $list);
                            break;
                        default:
                            break;
                    }
                    break;
                default :
                    break;
            }
        }
        return $products_query->skip($this->skip_count)->take($this->take_count);
    }

    /**
     * @return integer[]
     */
    public function getProductIds(): array
    {
        return $this->getQuery()->select("products.id")->pluck("id")->toArray();
    }

    public function getProducts()
    {
        return Product::mainModels()->visible()->whereIn("id", $this->getProductIds())->get();
    }

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = $value ?: "[]";
    }
}
