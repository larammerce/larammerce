<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/7/18
 * Time: 10:14 PM
 */

namespace App\Models;

use App\Enums\Discount\DiscountCardStatus;
use App\Exceptions\Discount\InvalidDiscountCodeException;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Properties
 * @property integer id
 * @property integer discount_group_id
 * @property integer customer_user_id
 * @property integer invoice_id
 * @property string client_ip
 * @property string code
 * @property boolean is_notified
 * @property boolean is_active
 *
 * Accessors
 * @property boolean is_multi
 * @property boolean is_event
 * @property boolean is_assigned
 * @property boolean is_percentage
 * @property DateTime expiration_date
 * @property boolean has_expiration
 *
 * Relations
 * @property DiscountGroup group
 * @property CustomerUser customer
 * @property Invoice[] invoices
 *
 * Timestamps
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * @method static DiscountCard find(integer $id)
 *
 * Class DiscountCard
 * @package App\Models
 *
 * TODO: invoice_id should be deleted. relation is moved into invoices table.
 */
class DiscountCard extends BaseModel
{
    protected $table = "discount_cards";
    protected $fillable = [
        "discount_group_id", "customer_user_id", "code"
    ];

    protected static array $SORTABLE_FIELDS = ["id"];

    static protected $FRONT_PAGINATION_COUNT = 10;

    public function getIsMultiAttribute(): bool
    {
        return $this->group->is_multi;
    }

    public function getIsPercentageAttribute(): bool
    {
        return $this->group->is_percentage;
    }

    public function getIsAssignedAttribute(): bool
    {
        return $this->group->is_assigned;
    }

    public function getExpirationDateAttribute(): DateTime
    {
        return $this->group->expiration_date;
    }

    public function getHasExpirationAttribute(): bool
    {
        return $this->group->has_expiration;
    }

    public function getHasDirectory(): bool
    {
        return $this->group->has_directory;
    }

    public function getIsEventAttribute(): bool
    {
        return $this->group->is_event;
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, "discount_card_id");
    }

    public function directories(): BelongsToMany
    {
        return $this->belongsToMany(Directory::class, "directory_discount_card", "discount_card_id", "directory_id");
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, "invoice_id");
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(DiscountGroup::class, "discount_group_id");
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class, "customer_user_id");
    }

    public function attachToInvoice(Invoice $invoice): bool
    {
        $this->customer_user_id = $invoice->customer_user_id;
        $invoice->discount_card_id = $this->id;
        try {
            //TODO: this needs to be transactional
            $this->save();
            $invoice->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function create(array $attributes = [])
    {
        if (!key_exists("try_count", $attributes))
            $attributes["try_count"] = 0;
        else
            $attributes["try_count"] += 1;

        if ($attributes["try_count"] > 5) {
            return null;
        }

        $attributes["code"] = (!isset($attributes["code"]) or $attributes["code"] == null) ?
            static::generateCode($attributes["prefix"] ?? "", $attributes["postfix"] ?? "") : $attributes["code"];

        try {
            return static::query()->create($attributes);
        } catch (Exception $e) {
            return static::create($attributes);
        }
    }

    public static function checkCode($discount_code, $guard = null)
    {

        /** @var DiscountCard $discount_card */
        $discount_card = DiscountCard::where("code", $discount_code)->first();
        $current_customer_user = get_customer_user($guard);

        if ($discount_card != null) {
            if (!$discount_card->is_active or !$discount_card->group->is_active) {
                throw new InvalidDiscountCodeException("The discount code `{$discount_code}` is inactive.",
                    DiscountCardStatus::INACTIVE);
            }

            if (!$discount_card->is_event and !$discount_card->is_multi and $discount_card->invoices()->count() != 0) {
                throw new InvalidDiscountCodeException("The discount code `{$discount_code}` is used.",
                    DiscountCardStatus::IS_USED);
            }

            if ($discount_card->is_event and !$discount_card->is_multi and $discount_card->invoices()->where("customer_user_id", $current_customer_user->id)->count() > 0) {
                throw new InvalidDiscountCodeException("The discount code `{$discount_code}` is used.",
                    DiscountCardStatus::IS_USED);
            }

            if (!$discount_card->group->is_event and $discount_card->customer_user_id != null) {
                if ($discount_card->customer_user_id != $current_customer_user->id) {
                    throw new InvalidDiscountCodeException("The discount code `{$discount_code}` is not for you.",
                        DiscountCardStatus::NOT_FOR_YOU);
                }
            }

            if ($discount_card->has_expiration and $discount_card->expiration_date != null and
                $discount_card->expiration_date < date("Y-m-d H:i:s")) {
                throw new InvalidDiscountCodeException("The discount code `{$discount_code}` is expired",
                    DiscountCardStatus::EXPIRED);
            }
            return $discount_card;
        } else {
            throw new InvalidDiscountCodeException("The discount code `{$discount_code}` not exists",
                DiscountCardStatus::NOT_EXIST);
        }
    }

    private static function generateCode(string $prefix, string $postfix): string
    {
        $newCode = $prefix . date("mdHi") . static::randomStr(4) . $postfix;
        return strtoupper($newCode);
    }

    private static function randomStr(int $length): string
    {
        $characters = "12346789ABCDEFGHJMNPQRTUXYZ";
        $charLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[mt_rand(0, $charLength - 1)];
        }
        return $randomString;
    }

    public function getSearchUrl(): string
    {
        return "";
    }

    public function matchesWithProduct(Product $product): bool
    {
        if (!$product->is_discountable or $product->has_discount or $product->discount_group_id != null)
            return false;
        if ($this->group->has_directory)
            return DB::table("directory_discount_card")->where("discount_card_id", $this->id)
                    ->join("directory_product", function ($join) use ($product) {
                        $join->on("directory_discount_card.directory_id", "=", "directory_product.directory_id")
                            ->where("product_id", $product->id);
                    })->count("directory_discount_card.directory_id") > 0;
        return true;
    }

}
