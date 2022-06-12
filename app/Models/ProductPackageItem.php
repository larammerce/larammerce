<?php

namespace App\Models;

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
 * Class ProductPackage
 * @package App\Models
 */
class ProductPackageItem extends BaseModel
{
    protected $table = 'product_package_items';

    protected $fillable = ["package_id", "product_id", "usage_count"];

    public $timestamps = false;


    /*
     * Relations Methods
     */

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }
}
