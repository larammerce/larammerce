<?php

namespace App\Models;

use App\Utils\CRMManager\Enums\CRMPersonType;
use App\Utils\CRMManager\Interfaces\CRMAccountInterface;
use App\Utils\CRMManager\Interfaces\CRMLeadInterface;
use App\Utils\CRMManager\Interfaces\CRMLineItemInterface;
use App\Utils\CRMManager\Interfaces\CRMOpportunityInterface;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

/**
 * @property integer id
 * @property integer user_id
 * @property string main_phone
 * @property string national_code
 * @property string fin_relation
 * @property boolean is_legal_person
 * @property boolean is_initiated
 * @property boolean is_active
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property integer credit
 * @property boolean is_cart_checked
 * @property string bank_account_card_number
 * @property string bank_account_uuid
 * @property string crm_lead_id
 * @property string crm_account_id
 * @property bool crm_must_push_op
 * @property string crm_op_id
 * @property Carbon crm_op_created_at
 * @property Carbon crm_op_updated_at
 *
 * @property User user
 * @property Product[] wishList
 * @property Product[] needList
 * @property CartRow[] cartRows
 * @property CustomerAddress[] addresses
 * @property Invoice[] invoices
 * @property CustomerUserLegalInfo legalInfo
 * @property CustomerAddress main_address
 *
 * Class CustomerUser
 * @package App\Models
 */
class CustomerUser extends BaseModel implements CRMLeadInterface, CRMAccountInterface, CRMOpportunityInterface {

    protected $table = 'customer_users';

    protected $fillable = [
        'user_id', 'main_phone', 'is_legal_person', 'national_code', 'is_initiated', 'fin_relation', 'is_active',
        'credit', 'is_cart_checked', 'bank_account_card_number', 'bank_account_uuid', "crm_lead_id", "crm_account_id",
        "crm_must_push_op", "crm_op_id", "crm_op_created_at", "crm_op_updated_at"
    ];

    protected $casts = [
        "is_cart_checked" => "bool",
        "is_active" => "bool",
        "is_initiated" => "bool",
        "is_legal_person" => "bool",
        "crm_must_push_op" => "bool",
    ];

    protected $dates = [
        "crm_op_created_at",
        "crm_op_updated_at"
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'main_phone', 'is_active', 'credit'];

    protected static array $SEARCHABLE_FIELDS = [
        'id',
        'main_phone',
        'national_code',
        'fin_relation',
        "crm_lead_id"
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function wishList(): BelongsToMany {
        return $this->belongsToMany(Product::class, 'customer_wish_lists',
            'customer_user_id', 'product_id');
    }

    public function needList(): BelongsToMany {
        return $this->belongsToMany(Product::class, 'customer_need_lists',
            'customer_user_id', 'product_id')->withTimestamps();
    }

    public function cartRows(): HasMany {
        return $this->hasMany(CartRow::class, 'customer_user_id');
    }

    public function addresses(): HasMany {
        return $this->hasMany(CustomerAddress::class, 'customer_user_id');
    }

    public function invoices(): HasMany {
        return $this->hasMany(Invoice::class, 'customer_user_id');
    }

    public function legalInfo(): HasOne {
        return $this->hasOne(CustomerUserLegalInfo::class, 'customer_user_id');
    }

    public function getMainAddressAttribute(): CustomerAddress|Model|null {
        return $this->addresses()->where("is_main", true)->first();
    }

    public function save(array $options = []): bool {
        $result = parent::save($options);

        if ($result) {
            if ($this->is_legal_person) {
                if ($this->legalInfo == null)
                    CustomerUserLegalInfo::create([
                        'customer_user_id' => $this->id
                    ]);
            } else {
                $this->legalInfo?->delete();
            }
        }

        return $result;
    }

    public function getFinManRelation($is_legal): bool|string {
        $relation = $this->fin_relation;
        if ($is_legal) {
            if ($this->is_legal_person and $this->legalInfo != null)
                $relation = $this->legalInfo->fin_relation;
            else
                return false;
        }
        return $relation;
    }

    public function updateFinManAddress($is_legal, $fullAddress, $stateId): bool {
        $config = [
            "full_address" => $fullAddress,
            "state_id" => $stateId,
        ];
        if ($is_legal) {
            if ($this->wasLegalPerson())
                return $this->user->updateFinManLegalCustomer($config);
            else {
                Log::error("customer_user_model.update_fin_man_address.is_legal_invoice_not_legal_person:customer:"
                    . $this->id);
                return false;
            }
        }
        return $this->user->updateFinManCustomer($config);
    }

    public function wasLegalPerson(): bool {
        return $this->is_legal_person and $this->legalInfo->fin_relation != "";
    }

    public function getSearchUrl(): string {
        return '';
    }

    public function metaItems(): HasMany {
        return $this->hasMany(CustomerMetaItem::class, "customer_user_id", "id");
    }

    function crmGetFullName(): string {
        return $this->user->full_name ?? "";
    }

    function crmGetFirstName(): string {
        return $this->user->name ?? "";
    }

    function crmGetLastName(): string {
        return $this->user->family ?? "";
    }

    function crmGetSource(): string {
        return "Website registration";
    }

    function crmGetMainPhone(): string {
        return $this->main_phone ?? "";
    }

    function crmGetSecondaryPhone(): string {
        return $this->main_address?->phone_number ?? "";
    }

    function crmHasSecondaryPhone(): bool {
        return !is_null($this->main_address);
    }

    function crmGetEmail(): string {
        return $this->user->email ?? "";
    }

    function crmGetLeadId(): string {
        return $this->crm_lead_id ?? "";
    }

    function crmSetLeadId(string $lead_id): void {
        $this->update([
            "crm_lead_id" => $lead_id
        ]);
    }

    function crmGetPersonType(): string {
        return CRMPersonType::INDIVIDUAL;
    }

    function crmGetCreatedAt(): Carbon {
        return $this->created_at ?? Carbon::now();
    }

    function crmGetAccountId(): string {
        return $this->crm_account_id ?? "";
    }

    function crmSetAccountId(string $account_id): void {
        $this->update(["crm_account_id" => $account_id]);
    }

    public function crmGetOpId(): string {
        return $this->crm_op_id;
    }

    public function crmSetOpId(string $op_id) {
        $this->update([
            "crm_op_id" => $op_id
        ]);
    }

    public function crmGetOpName(): string {
        return $this->user->full_name . " - " . trans("crm.entities.opportunity");
    }

    /**
     * @return array<CRMLineItemInterface>
     */
    public function crmGetLineItems(): array {
        return $this->cartRows;
    }

    public function crmGetOpAmount(): float {
        return $this->cartRows()->with("product")->get()->sum(function (CartRow $cart_row) {
            return $cart_row->product->getStandardLatestPrice() * $cart_row->count;
        });
    }

    public function crmGetOpCreatedAt(): Carbon {
        return $this->created_at;
    }

    public function crmGetOpUpdatedAt(): Carbon {
        return $this->updated_at;
    }

    public function crmSetOpRelCreatedAt(Carbon $created_at): void {
        $this->update([
            "crm_op_created_at" => $created_at
        ]);
    }

    public function crmGetOpRelCreatedAt(): Carbon {
        return $this->crm_op_created_at;
    }

    public function crmSetOpRelUpdatedAt(Carbon $updated_at): void {
        $this->update([
            "crm_op_updated_at" => $updated_at
        ]);
    }

    public function crmGetOpRelUpdatedAt(): Carbon {
        return $this->crm_op_updated_at;
    }
}
