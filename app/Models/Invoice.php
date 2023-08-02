<?php

namespace App\Models;

use App\Enums\Invoice\NewInvoiceType;
use App\Enums\Invoice\PaymentStatus;
use App\Services\Invoice\NewInvoiceService;
use App\Utils\CMS\ProductService;
use App\Utils\FinancialManager\Exceptions\FinancialDriverInvalidConfigurationException;
use App\Utils\FinancialManager\Factory;
use App\Utils\ShipmentService\Factory as ShipmentFactory;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property integer id
 * @property integer status
 *
 * @property integer payment_type
 * @property integer payment_status
 *
 * @property int shipment_cost
 * @property integer shipment_method
 * @property integer shipment_status
 * @property string shipment_driver
 * @property string shipment_data
 *
 * @property integer customer_user_id
 * @property string customer_address
 * @property string transferee_name
 * @property string phone_number
 * @property integer state_id
 * @property string tracking_code
 *
 * @property boolean has_paper
 * @property boolean has_shipment_cost
 *
 * @property boolean is_active
 * @property boolean is_warned
 * @property boolean is_legal
 * @property boolean is_shippable
 * @property boolean is_ship_free
 *
 * @property string payment_id
 * @property string fin_relation
 *
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * @property DateTime survey_notified_at
 * @property DateTime survey_viewed_at
 *
 * @property integer discount_card_id
 * @property integer sum
 * @property integer try_count
 *
 * @property integer direct_discount
 * @property int sum_of_rows
 *
 * @property State state
 * @property Product[] products
 * @property CustomerUser customer
 * @property InvoiceRow[] rows
 * @property DiscountCard discountCard
 *
 * @method static Invoice find(integer $id)
 *
 * Class Invoice
 * @package App\Models
 */
class Invoice extends BaseModel {
    protected $table = 'invoices';
    protected $fillable = [
        'payment_type', 'customer_user_id', 'customer_address', 'sum', 'payment_status', 'payment_id', 'has_paper',
        'shipment_method', 'shipment_status', 'phone_number', 'transferee_name', 'fin_relation', 'is_legal', 'state_id',
        'is_active', 'is_warned', 'has_shipment_cost', 'shipment_driver', 'shipment_data', 'discount_card_id',
        'survey_notified_at', 'survey_viewed_at'
    ];
    protected $casts = [
        "survey_notified_at" => "timestamp",
        "survey_viewed_at" => "timestamp"
    ];
    protected array $extra_attributes = [
        "status" => 0
    ];

    protected array $cached_attributes = [];

    protected static array $SORTABLE_FIELDS = ['id', 'payment_type', 'payment_status', 'shipment_status', 'sum', 'is_active'];
    protected static array $SEARCHABLE_FIELDS = ['tracking_code'];
    protected static int $FRONT_PAGINATION_COUNT = 10;

    private NewInvoiceService $new_invoice_service;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->new_invoice_service = app(NewInvoiceService::class);
    }

    public function setNewInvoiceService(NewInvoiceService $new_invoice_service): void {
        $this->new_invoice_service = $new_invoice_service;
    }

    public function getStatusAttribute(): int {
        return $this->extra_attributes["status"] ?? 0;
    }

    public function setStatusAttribute(int $status): void {
        if (!in_array($status, NewInvoiceType::values()))
            return;
        $this->extra_attributes["status"] = $status;
    }

    public function getHasShipmentCostAttribute(): int {
        return $this->shipment_cost > 0;
    }

    public function getIsShippableAttribute(): bool {
        foreach ($this->rows as $row) {
            if ($row->product->productStructure->is_shippable)
                return true;
        }
        return false;
    }

    public function getIsShipFreeAttribute(): bool {
        return !$this->is_shippable or $this->new_invoice_service->getMinimumPurchaseFreeShipment() <= $this->sum;
    }

    public function getSumOfRowsAttribute(): int {
        if (!isset($this->cached_attributes["sum_of_rows"])) {
            $result = DB::table("invoice_rows")->whereRaw(DB::raw("invoice_id={$this->id}"))
                ->select(DB::raw("sum((pure_price + tax_amount + toll_amount) * count) as sum_of_rows"))
                ->first();
            $this->cached_attributes["sum_of_rows"] = $result != null ? $result->sum_of_rows : 0;
        }
        return $this->cached_attributes["sum_of_rows"];
    }

    public function products(): BelongsToMany {
        return $this->belongsToMany(Product::class, 'invoice_rows',
            'invoice_id', 'product_id');
    }

    public function customer(): BelongsTo {
        return $this->belongsTo(CustomerUser::class, 'customer_user_id');
    }

    public function state(): BelongsTo {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function rows(): HasMany {
        return $this->hasMany(InvoiceRow::class, 'invoice_id');
    }

    public function payments(): HasMany {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function discountCard(): BelongsTo {
        return $this->belongsTo(DiscountCard::class, 'discount_card_id');
    }

    public function createFinManRelation(): bool {
        if ($this->is_active)
            return false;

        if ($this->customer->fin_relation == null or strlen($this->customer->fin_relation) == 0) {
            try {
                $this->customer->user->saveFinManCustomer();
            } catch (FinancialDriverInvalidConfigurationException $e) {
                return false;
            }
        }

        $preInvoiceAddResult = Factory::driver()->addPreInvoice($this);
        if ($preInvoiceAddResult === false) {
            return false;
        }

        $this->is_active = true;
        $this->fin_relation = $preInvoiceAddResult;
        $this->save();

        return true;
    }

    public function deleteFinManRelation(): bool {
        if ((!$this->is_active) or
            $this->payment_status == PaymentStatus::SUBMITTED or
            $this->payment_status == PaymentStatus::CONFIRMED)
            return false;
        if (Factory::driver()->deletePreInvoice($this->fin_relation)) {
            $this->try_count += 1;
            $this->fin_relation = null;
            $this->is_active = false;
            $this->is_warned = false;
            $this->save();
            return true;
        } else {
            $this->try_count += 1;
            if ($this->try_count > 3) {
                $this->delete();
                return true;
            } else {
                $this->save();
                return false;
            }
        }
    }

    public function hasDiscountCard(): bool {
        return $this->discountCard != null;
    }

    public function updateRows() {
        $discount_percentage = 0;
        $discount_card = $this->discountCard;
        $discountable_amount = $this->getDiscountableAmount();

        if ($discount_card != null) {
            $discount_value = $discount_card->group->calculate($discountable_amount);
            if ($discount_card->group->is_percentage)
                $discount_percentage = $discount_value;
            else
                $this->direct_discount = ($discount_value / $this->new_invoice_service->getProductPriceRatio());
        }

        $this->sum = 0;
        $rowsToRemoveIds = [];

        foreach ($this->rows as $invoiceRow) {
            if (isset($invoiceRow->product) and
                $invoiceRow->product->is_active and
                $invoiceRow->product->count > $invoiceRow->product->min_allowed_count and
                $invoiceRow->count <= $invoiceRow->product->getMaximumAllowedPurchaseCount()) {
                $invoiceRow->updateAmounts($discount_card, $discount_percentage);
                $this->sum += $invoiceRow->sum();
            } else
                $rowsToRemoveIds[] = $invoiceRow->id;
        }

        if (sizeof($rowsToRemoveIds) > 0)
            $this->rows()->whereIn('id', $rowsToRemoveIds)->delete();

        $this->shipment_cost = $this->calculateShipmentCost();
        $this->sum += $this->shipment_cost;

        if ($this->sum > $this->direct_discount and $this->direct_discount > 0) {
            $this->sum -= $this->direct_discount;
        }
    }

    public static function getFrontPaginationCount(): int {
        return self::$FRONT_PAGINATION_COUNT;
    }

    public function getSearchUrl(): string {
        return route("admin.invoice.edit", $this);
    }

    public function isPayed(): bool {
        return in_array($this->payment_status, [
            PaymentStatus::SUBMITTED,
            PaymentStatus::CONFIRMED
        ]);
    }

    public function hasTrackableShipment(): bool {
        if (!$this->shipment_driver)
            return false;

        return ShipmentFactory::driver($this->shipment_driver)->isTrackable();
    }

    public function getShipmentTrackingCode(): string {
        if (!$this->shipment_driver)
            return '';

        return ShipmentFactory::driver($this->shipment_driver)->getTrackingCode($this->shipment_data);
    }

    public function getShipmentTrackingUrl(): string {
        if (!$this->shipment_driver)
            return '#';

        return ShipmentFactory::driver($this->shipment_driver)->getTrackingUrl();
    }

    public function getShipmentDeliveryDate(): string {
        if (!$this->shipment_driver)
            return '-';

        return ShipmentFactory::driver($this->shipment_driver)->getDeliveryDate($this->shipment_data);
    }

    public function getDiscountAmount(): int {
        $discount = 0;
        foreach ($this->rows as $invoiceRow)
            $discount += $invoiceRow->discount_amount * $invoiceRow->count;
        return $discount;
    }

    public function getDiscountPercentage(): int {
        $discountCard = $this->discountCard;
        $discountGroup = $discountCard?->group;
        if ($discountGroup != null and $discountGroup->is_percentage)
            return $discountGroup->value;
        return 0;
    }

    public function getCMIComment($newline = "\n"): string {
        $result = "";
        foreach ($this->rows()->where("cmi_id", "!=", null)->get() as $row) {
            $result .= "({$row->product->title}): {$newline} ================== {$newline} " .
                $row->getCMIComment($newline);
        }
        return $result;
    }

    public function getDiscountableAmount(): int {
        $discountable_amount = 0;
        if ($this->discount_card_id !== null) {
            foreach ($this->rows as $row) {
                $product = $row->product;
                if ($this->discountCard->matchesWithProduct($product))
                    $discountable_amount += ($product->latest_price * $row->count);
            }
        }
        return $discountable_amount;
    }

    public function calculateShipmentCost(): int {
        if ($this->is_ship_free)
            return 0;
        return $this->new_invoice_service->getStandardShipmentCost($this->state_id);
    }

    public function updateAddress(?CustomerAddress $customer_address) {
        if ($customer_address !== null) {
            $this->state_id = $customer_address->state_id;
            $this->customer_address = $customer_address->getFullAddress();
            $this->phone_number = $customer_address->phone_number ?: $customer_address->customer->main_phone;
            $this->transferee_name = $customer_address->transferee_name;
        } else {
            $this->state_id = 0;
        }
        $this->shipment_cost = $this->calculateShipmentCost();
    }

    public function customPush(): bool {
        if (!$this->save()) {
            return false;
        }

        foreach ($this->relations as $models) {
            $models = $models instanceof Collection
                ? $models->all() : [$models];

            foreach (array_filter($models) as $model) {
                if (in_array("invoice_id", $model->getFillable())) {
                    $model->invoice_id = $this->id;
                }
                if (!$model->push()) {
                    return false;
                }
            }
        }

        return true;
    }
}
