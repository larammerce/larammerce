<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * @property integer id
 * @property integer usage_count
 * @property integer package_id
 * @property integer product_id
 *
 * @property Product product
 * @property ProductPackage package
 *
 * Class ProductPackage
 * @package App\Models
 */
class ProductPackageItem extends BaseModel
{
    protected $table = 'product_package_items';

    protected $fillable = ["package_id", "product_id", "usage_count"];

    public $timestamps = false;

    public function getSearchUrl(): string {
        return '';
    }

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class, "product_id", "id");
    }

    public function package(): BelongsTo {
        return $this->belongsTo(ProductPackage::class, "package_id", "id");
    }
}
