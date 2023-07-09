<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use DateTime;

/**
 * @property integer id
 * @property integer customer_user_id
 * @property integer product_id
 * @property DateTime created_at
 * @property DateTime updated_at
 * @property Product product
 * @property CustomerUser customer
 *
 * Class CartRow
 * @package App\Models
 */
class NeedList extends BaseModel
{
    protected $table = 'customer_need_lists';
    protected $fillable = [
        'customer_user_id', 'product_id'
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'created_at'];
    static protected int $FRONT_PAGINATION_COUNT = 10;

    protected static array $EXPORTABLE_RELATIONS = [
        Product::class => [
            "name" => "product",
            "fields" => [
                "code",
                "title"
            ]
        ]
    ];


    public function customer(): BelongsTo {
        return $this->belongsTo(CustomerUser::class, "customer_user_id");
    }

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class, "product_id");
    }

    public function getSearchUrl(): string {
        return "";
    }
}
