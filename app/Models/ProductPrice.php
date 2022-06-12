<?php

namespace App\Models;

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
class ProductPrice extends BaseModel
{
    protected $table = 'product_prices';
    public $timestamps = true;

    protected $fillable = [
        'product_id', 'value'
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'value'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('\\App\\Models\\Product', 'product_id');
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }
}
