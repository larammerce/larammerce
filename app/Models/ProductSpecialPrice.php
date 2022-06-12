<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 * @property integer id
 * @property integer product_id
 * @property integer value
 *
 * @property Product product
 *
 * Class ProductPrice
 * @package App\Models
 */
class ProductSpecialPrice extends BaseModel
{
    protected $table = 'product_special_prices';
    public $timestamps = true;

    protected $fillable = [
        'product_id', 'value'
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'value'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }
}
