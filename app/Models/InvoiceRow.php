<?php

namespace App\Models;


use App\Services\Invoice\NewInvoiceService;
use App\Utils\CMS\ProductService;
use App\Utils\FinancialManager\ConfigProvider;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer id
 * @property integer invoice_id
 * @property integer product_id
 * @property integer discount_amount
 * @property integer tax_amount
 * @property integer toll_amount
 * @property integer count
 * @property integer product_price
 * @property integer pure_price
 * @property string description
 * @property integer cmi_id
 *
 * @property Product product
 * @property Invoice invoice
 * @property CustomerMetaItem customerMetaItem
 *
 * Class Invoice
 * @package App\Models
 */
class InvoiceRow extends BaseModel
{
    protected $table = 'invoice_rows';

    protected $fillable = [
        'invoice_id', 'product_id', 'discount_amount', 'count', 'description', 'product_price', 'tax_amount',
        'toll_amount', "cmi_id", "pure_price"
    ];

    public $timestamps = false;

    protected static array $SORTABLE_FIELDS = ['id', 'count'];

    private NewInvoiceService $new_invoice_service;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->new_invoice_service = app(NewInvoiceService::class);

    }


    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * @return BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function customerMetaItem(): BelongsTo
    {
        return $this->belongsTo(CustomerMetaItem::class, "cmi_id", "id");
    }

    public function purePrice()
    {
        return $this->pure_price;
    }

    public function shownPrice()
    {
        return $this->product_price;
    }

    public function paymentPrice()
    {
        return $this->pure_price + $this->tax_amount + $this->toll_amount;
    }

    public function sum()
    {
        return $this->paymentPrice() * $this->count;
    }

    public function updateAmounts(?DiscountCard $discount_card = null, int $discount_percentage = 0)
    {
        $product = $this->product;
        $this->discount_amount = 0;
        $this->tax_amount = $product->tax_amount;
        $this->toll_amount = $product->toll_amount;
        $this->pure_price = $product->pure_price;
        $this->product_price = $this->paymentPrice();

        if ($this->product->is_discountable) {
            if ($this->product->has_discount) {
                $this->applySpecialOffer();
            } else if ($product->discountGroup !== null) {
                $discount_group = $product->discountGroup;
                $discount_value = $discount_group->calculate($product->latest_price * $this->count);
                if ($discount_group->is_percentage) {
                    $this->applyDiscountPercentage($discount_value);
                }
            } else {
                if ($discount_card !== null and $discount_percentage > 0 and $discount_card->matchesWithProduct($product))
                    $this->applyDiscountPercentage($discount_percentage);
            }
        }
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }

    public function getCMIComment($newline = "\n")
    {
        $result = $newline;
        if ($this->cmi_id !== null) {
            $customer_meta_item = $this->customerMetaItem;
            foreach ($customer_meta_item->category->data_object as $field) {
                if (isset($customer_meta_item->data_object->{$field->input_identifier}))
                    $result .= $field->input_title . " : " . $customer_meta_item->data_object->{$field->input_identifier} . $newline;
            }
        }

        return $result;
    }

    private function applyDiscountPercentage($discount_percentage): void {
        $this->product_price = $this->product->latest_price / $this->new_invoice_service->getProductPriceRatio();
        $this->discount_amount = intval($this->product_price * $discount_percentage / 100);

        $price_data = ConfigProvider::isTaxAddedToPrice() ?
            $this->new_invoice_service->reverseCalculateProductTaxAndToll($this->product_price - $this->discount_amount) :
            $this->new_invoice_service->calculateProductTaxAndToll($this->product_price - $this->discount_amount);

        $this->pure_price = $price_data->price;
        $this->tax_amount = $price_data->tax;
        $this->toll_amount = $price_data->toll;
    }

    private function applySpecialOffer(): void {
        $this->product_price = $this->product->previous_price / $this->new_invoice_service->getProductPriceRatio();
        $this->discount_amount = $this->product_price - ($this->product->latest_price / $this->new_invoice_service->getProductPriceRatio());

        $price_data = ConfigProvider::isTaxAddedToPrice() ?
            $this->new_invoice_service->reverseCalculateProductTaxAndToll($this->product_price - $this->discount_amount) :
            $this->new_invoice_service->calculateProductTaxAndToll($this->product_price - $this->discount_amount);

        $this->pure_price = $price_data->price;
        $this->tax_amount = $price_data->tax;
        $this->toll_amount = $price_data->toll;
    }
}
