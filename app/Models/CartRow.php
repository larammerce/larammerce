<?php


namespace App\Models;

use App\Services\Invoice\NewInvoiceService;
use App\Utils\CRMManager\Enums\CRMLineItemDiscountType;
use App\Utils\CRMManager\Interfaces\CRMLineItemInterface;
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
class CartRow extends BaseModel implements CRMLineItemInterface {
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

    public function crmGetLineItemId(): int {
        return $this->product_id;
    }

    public function crmGetLineItemCode(): string {
        return $this->product->code;
    }

    public function crmGetLineItemName(): string {
        return $this->product->title;
    }

    public function crmGetLineItemListPrice(): float {
        return $this->product->getSTDPurePrice();
    }

    public function crmGetLineItemSubTotal(): float {
        return $this->product->getSTDPurePrice() * $this->count;
    }

    public function crmGetLineItemDiscountType(): string {
        $product = $this->product;
        if ($product->is_discountable) {
            if ($product->has_discount) {
                if ($product->latest_special_price !== $product->latest_price) {
                    return CRMLineItemDiscountType::AMOUNT;
                }
            } else {
                if (!is_null($product->discountGroup)) {
                    if ($product->discountGroup->is_percentage) {
                        return CRMLineItemDiscountType::PERCENTAGE;
                    } else {
                        return CRMLineItemDiscountType::AMOUNT;
                    }
                }
            }
        }
        return CRMLineItemDiscountType::AMOUNT;
    }

    public function crmGetLineItemDiscountValue(): float {
        $product = $this->product;
        if ($product->is_discountable) {
            if ($product->has_discount) {
                if ($product->pure_price !== $product->getSTDPurePrice()) {
                    return $product->getSTDPurePrice() - $product->pure_price;
                }
            } else {
                if (!is_null($product->discountGroup)) {
                    return $product->discountGroup->value;
                }
            }
        }
        return 0;
    }

    public function crmGetLineItemProductDiscountAmount(): float {
        if ($this->crmGetLineItemDiscountType() === CRMLineItemDiscountType::AMOUNT) {
            return $this->crmGetLineItemDiscountValue();
        } else if ($this->crmGetLineItemDiscountType() === CRMLineItemDiscountType::PERCENTAGE) {
            return $this->crmGetLineItemListPrice() * $this->crmGetLineItemDiscountValue() / 100;
        } else {
            return 0;
        }
    }

    public function crmGetLineItemProductUnitPrice(): float {
        return $this->crmGetLineItemListPrice() - $this->crmGetLineItemProductDiscountAmount();
    }

    public function crmGetLineItemVatPercentage(): int {
        /** @var NewInvoiceService $new_invoice_service */
        $new_invoice_service = app(NewInvoiceService::class);
        return $new_invoice_service->getProductAllExtrasPercentage($this->product);
    }

    public function crmGetLineItemVatAmount(): float {
        /** @var NewInvoiceService $new_invoice_service */
        $new_invoice_service = app(NewInvoiceService::class);
        $std_result = $new_invoice_service->calculateProductTaxAndToll($this->crmGetLineItemProductUnitPrice(), $this->count, $this->product);
        return ($std_result->tax ?? 0) + ($std_result->toll ?? 0);
    }

    public function crmGetLineItemGrandTotal(): float {
        return $this->crmGetLineItemProductUnitPrice() + $this->crmGetLineItemVatAmount();
    }

    public function crmGetLineItemQuantity(): float {
        return $this->count;
    }
}
