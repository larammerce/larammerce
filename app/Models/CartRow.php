<?php


namespace App\Models;

use App\Utils\CRMManager\Enums\CRMOpItemDiscountType;
use App\Utils\CRMManager\Interfaces\CRMOpItemInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use DateTime;

/**
 * @property integer $id
 * @property integer customer_user_id
 * @property integer product_id
 * @property integer count
 * @property integer cmi_id
 * @property DateTime created_at
 * @property DateTime updated_at
 * @property DateTime customer_notified_at
 * @property DateTime customer_viewed_at
 * @property Product product
 * @property CustomerUser customer
 * @property CustomerMetaItem customerMetaItem
 *
 * Class CartRow
 * @package App\Models
 */
class CartRow extends BaseModel implements CRMOpItemInterface {
    protected $table = 'customer_carts';
    protected $fillable = [
        'customer_user_id', 'product_id', 'count', "cmi_id",
        'customer_notified_at', 'customer_viewed_at'
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'customer_user_id'];
    static protected int $FRONT_PAGINATION_COUNT = 10;

    protected $casts = [
        "customer_notified_at" => "timestamp",
        "customer_viewed_at" => "timestamp"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer() {
        return $this->belongsTo("\\App\\Models\\CustomerUser", "customer_user_id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product() {
        return $this->belongsTo("\\App\\Models\\Product", "product_id");
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string {
        return "";
    }

    public function customerMetaItem(): BelongsTo {
        return $this->belongsTo(CustomerMetaItem::class, "cmi_id", "id");
    }

    public function crmGetOpItemId(): int {
        return $this->product_id;
    }

    public function crmGetOpItemCode(): string {
        return $this->product->code;
    }

    public function crmGetOpItemName(): string {
        return $this->product->title;
    }

    public function crmGetOpListPrice(): float {
        return $this->product->getStandardLatestPrice();
    }

    public function crmGetOpSubTotal(): float {
        return $this->crmGetOpListPrice() * $this->count;
    }

    public function crmGetOpDiscountType(): float {
        $product = $this->product;
        if ($product->has_discount) {
            if ($product->latest_special_price !== $product->latest_price) {
                return CRMOpItemDiscountType::AMOUNT;
            }
        } else {
            if ($product->is_discountable and !is_null($product->discountGroup)) {
                if ($product->discountGroup->is_percentage) {
                    return CRMOpItemDiscountType::PERCENTAGE;
                } else {
                    return CRMOpItemDiscountType::AMOUNT;
                }
            }
        }
        return CRMOpItemDiscountType::AMOUNT;
    }

    public function crmGetOpProductDiscountAmount(): float {
        $product = $this->product;
        if ($product->has_discount) {
            if ($product->latest_special_price !== $product->latest_price) {
                return $product->getStandardLatestPrice() - $product->getStandardSpecialPrice();
            }
        } else {
            if ($product->is_discountable and !is_null($product->discountGroup)) {
                if ($product->discountGroup->is_percentage) {
                    $discount_percentage = $product->discountGroup->value;
                    return (int)($product->getStandardLatestPrice() * $discount_percentage / 100);
                } else {
                    return $product->discountGroup->value;
                }
            }
        }
        return CRMOpItemDiscountType::AMOUNT;
    }

    public function crmGetOpProductUnitPrice(): float {
        // TODO: Implement crmGetOpProductUnitPrice() method.
    }

    public function crmGetOpVatPercentage(): int {
        // TODO: Implement crmGetOpVatPercentage() method.
    }

    public function crmGetOpVatAmount(): float {
        // TODO: Implement crmGetOpVatAmount() method.
    }

    public function crmGetOpGrandTotal(): float {
        // TODO: Implement crmGetOpGrandTotal() method.
    }
}
