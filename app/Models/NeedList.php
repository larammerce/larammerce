<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use DateTime;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;

/**
 * @property integer id
 * @property integer customer_user_id
 * @property integer user_id
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
        'customer_user_id', 'product_id', 'user_id'
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'created_at'];
    static protected int $FRONT_PAGINATION_COUNT = 10;

    protected $hidden = [
        "updated_at"
    ];

    protected static array $EXPORTABLE_RELATIONS = [
        Product::class => [
            "name" => "product",
            "fields" => [
                "code",
                "title"
            ]
        ],
        CustomerUser::class => [
            "name" => "customer",
            "fields" => [
                "main_phone",
            ]
        ],
        User::class => [
            "name" => "user",
            "fields" => [
                "name",
                "family"
            ]
        ]
    ];

    public static function syncWithoutDetaching(Product $product, CustomerUser $customer): ?NeedList {
        if (NeedList::query()->where("product_id", $product->id)->where("customer_user_id", $customer->id)->count() > 0) {
            return null;
        }
        return NeedList::create([
            "product_id" => $product->id,
            "customer_user_id" => $customer->id,
            "user_id" => $customer->user_id
        ]);
    }


    public function customer(): BelongsTo {
        return $this->belongsTo(CustomerUser::class, "customer_user_id");
    }

    public function user() {
        return $this->belongsTo(User::class, "user_id");
    }

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class, "product_id");
    }

    public function getSearchUrl(): string {
        return "";
    }
}
