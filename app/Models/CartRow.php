<?php


namespace App\Models;

use App\Services\Invoice\NewInvoiceService;
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

    public function crmGetOpDiscountType(): string {
        $product = $this->product;
        if ($product->is_discountable) {
            if ($product->has_discount) {
                if ($product->latest_special_price !== $product->latest_price) {
                    return CRMOpItemDiscountType::AMOUNT;
                }
            } else {
                if (!is_null($product->discountGroup)) {
                    if ($product->discountGroup->is_percentage) {
                        return CRMOpItemDiscountType::PERCENTAGE;
                    } else {
                        return CRMOpItemDiscountType::AMOUNT;
                    }
                }
            }
        }
        return CRMOpItemDiscountType::AMOUNT;
    }

    public function crmGetOpDiscountValue(): float {
        $product = $this->product;
        if ($product->is_discountable) {
            if ($product->has_discount) {
                if ($product->latest_special_price !== $product->latest_price) {
                    return $product->getStandardLatestPrice() - $product->getStandardSpecialPrice();
                }
            } else {
                if (!is_null($product->discountGroup)) {
                    return $product->discountGroup->value;
                }
            }
        }
        return 0;
    }

    public function crmGetOpProductDiscountAmount(): float {
        $product = $this->product;
        if ($product->is_discountable) {
            if ($product->has_discount) {
                if ($product->latest_special_price !== $product->latest_price) {
                    return $product->getStandardLatestPrice() - $product->getStandardSpecialPrice();
                }
            } else {
                if (!is_null($product->discountGroup)) {
                    if ($product->discountGroup->is_percentage) {
                        $discount_percentage = $product->discountGroup->value;
                        return (int)($product->getStandardLatestPrice() * $discount_percentage / 100);
                    } else {
                        return $product->discountGroup->value;
                    }
                }
            }
        }
        return 0;
    }

    public function crmGetOpProductUnitPrice(): float {
        return $this->crmGetOpListPrice() - $this->crmGetOpProductDiscountAmount();
    }

    public function crmGetOpVatPercentage(): int {
        /** @var NewInvoiceService $new_invoice_service */
        $new_invoice_service = app(NewInvoiceService::class);
        return $new_invoice_service->getProductAllExtrasPercentage($this->product);
    }

    public function crmGetOpVatAmount(): float {
        /** @var NewInvoiceService $new_invoice_service */
        $new_invoice_service = app(NewInvoiceService::class);
        return $new_invoice_service->getProductAllExtrasAmount($this->product);
    }

    public function crmGetOpGrandTotal(): float {
        // TODO: Implement crmGetOpGrandTotal() method.
    }

    public function crmGetOpItemPrice(): float {
        // TODO: Implement crmGetOpItemPrice() method.
    }

    public function crmGetOpItemQuantity(): float {
        // TODO: Implement crmGetOpItemQuantity() method.
    }

    public function crmGetOpItemAmount(): float {
        // TODO: Implement crmGetOpItemAmount() method.
    }
}
