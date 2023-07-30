<?php

namespace App\Models;

use App\Exceptions\Product\ProductPackageItemNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * @property integer id
 * @property integer product_id
 * @property Product product
 * @property Product[] products
 * @property ProductPackageItem[] productPackageItems
 *
 * Class ProductPackage
 * @package App\Models
 */
class ProductPackage extends BaseModel
{
    protected $table = 'product_packages';

    protected $fillable = [];

    public $timestamps = false;


    /*
     * Relations Methods
     */

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, "product_package_items",
            "package_id", "product_id");
    }

    public function productPackageItems(): HasMany
    {
        return $this->hasMany(ProductPackageItem::class, "package_id");
    }

    /**
     * @return array
     * @throws ProductPackageItemNotFoundException
     */
    public function getPackageItems(): array
    {
        $package_items = [];
        foreach ($this->products as $product) {
            $item = new stdClass();
            $item->product_id = $product->id;
            $item->product_title = $product->title;
            $item->product_count = $this->getItemUsageCount($product->id);
            array_push($package_items, $item);
        }
        return $package_items;
    }

    /**
     * @param int $product_id
     * @return int
     * @throws ProductPackageItemNotFoundException
     */
    public function getItemUsageCount(int $product_id): int
    {
        $item = $this->productPackageItems()->where("product_id", $product_id)->first();
        if ($item == null)
            throw new ProductPackageItemNotFoundException();
        return $item->usage_count;
    }

    /**
     * @param int $product_id
     * @param int $count
     * @throws ProductPackageItemNotFoundException
     */
    public function setProductUsageCount(int $count, int $product_id)
    {
        $item = $this->productPackageItems()->where("product_id", $product_id)->first();
        if ($item == null)
            throw new ProductPackageItemNotFoundException();
        $item->usage_count = $count;
        $item->save();
    }

    /**
     * @return int
     */
    //    count_total = min(count/usage_count, ..)
    public function getCount(): int
    {
        $min = DB::table(DB::raw("(select case when ppi.usage_count = 0 then 0 else (pr.count / ppi.usage_count) end as calc_count " .
            "from product_package_items as ppi inner join products as pr on ppi.product_id = pr.id " .
            "where ppi.package_id = {$this->id}) as package_counts"))->min("calc_count");
        if ($min != null)
            return $min;
        return 0;
    }

    /**
     * @return int
     */
    //    price = sigma (count*latest_price)
    public function getLatestPrice(): int
    {
        return DB::table(DB::raw("(select (pr.latest_price * ppi.usage_count) as calc_price " .
            "from product_package_items as ppi inner join products as pr on ppi.product_id = pr.id " .
            "where ppi.package_id = {$this->id}) as package_prices"))->sum("calc_price");
    }

    public function getPurePrice(): int
    {
        return DB::table(DB::raw("(select (pr.pure_price * ppi.usage_count) as calc_price " .
            "from product_package_items as ppi inner join products as pr on ppi.product_id = pr.id " .
            "where ppi.package_id = {$this->id}) as package_prices"))->sum("calc_price");
    }

    public function getTaxAmount(): int
    {
        return DB::table(DB::raw("(select (pr.tax_amount * ppi.usage_count) as calc_price " .
            "from product_package_items as ppi inner join products as pr on ppi.product_id = pr.id " .
            "where ppi.package_id = {$this->id}) as package_prices"))->sum("calc_price");
    }

    public function getTollAmount(): int
    {
        return DB::table(DB::raw("(select (pr.toll_amount * ppi.usage_count) as calc_price " .
            "from product_package_items as ppi inner join products as pr on ppi.product_id = pr.id " .
            "where ppi.package_id = {$this->id}) as package_prices"))->sum("calc_price");
    }

    /**
     * @return int
     */
    //    price = sigma (count*latest_price)
    public function getPreviousPrice(): int
    {
        return DB::table(DB::raw("(select (pr.previous_price * ppi.usage_count) as calc_pre_price " .
            "from product_package_items as ppi inner join products as pr on ppi.product_id = pr.id " .
            "where ppi.package_id = {$this->id}) as package_prices"))->sum("calc_pre_price");
    }

    /**
     * @return int
     */
    //    price = sigma (count*latest_price)
    public function getLatestSpecialPrice(): int
    {
        return DB::table(DB::raw("(select (pr.latest_special_price * ppi.usage_count) as calc_spec_price " .
            "from product_package_items as ppi inner join products as pr on ppi.product_id = pr.id " .
            "where ppi.package_id = {$this->id}) as package_prices"))->sum("calc_spec_price");
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }
}
