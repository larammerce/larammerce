<?php

namespace App\Models;

use App\Utils\CRMManager\Enums\CRMLeadType;
use App\Utils\CRMManager\Interfaces\CRMLeadInterface;
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
 * @property string crm_relation
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
class CustomerUser extends BaseModel implements CRMLeadInterface {

    protected $table = 'customer_users';

    protected $fillable = [
        'user_id', 'main_phone', 'is_legal_person', 'national_code', 'is_initiated', 'fin_relation', 'is_active',
        'credit', 'is_cart_checked', 'bank_account_card_number', 'bank_account_uuid', "crm_relation"
    ];

    protected $casts = [
        "is_cart_checked" => "bool",
        "is_active" => "bool",
        "is_initiated" => "bool",
        "is_legal_person" => "bool"
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'main_phone', 'is_active', 'credit'];

    protected static array $SEARCHABLE_FIELDS = [
        'id',
        'main_phone',
        'national_code',
        'fin_relation',
        "crm_relation"
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

    function leadGetFullName(): string {
        return $this->user->full_name ?? "";
    }

    function leadGetFirstName(): string {
        return $this->user->name ?? "";
    }

    function leadGetLastName(): string {
        return $this->user->family ?? "";
    }

    function leadGetSource(): string {
        return "Website registration";
    }

    function leadGetMainPhone(): string {
        return $this->main_phone ?? "";
    }

    function leadGetSecondaryPhone(): string {
        return $this->main_address?->phone_number ?? "";
    }

    function leadHasSecondaryPhone(): bool {
        return !is_null($this->main_address);
    }

    function leadGetEmail(): string {
        return $this->user->email ?? "";
    }

    function leadGetRelation(): string {
        return $this->crm_relation ?? "";
    }

    function leadSetRelation(string $lead_id): void {
        $this->update([
            "crm_relation" => $lead_id
        ]);
    }

    function leadGetType(): string {
        return CRMLeadType::INDIVIDUAL;
    }

    function leadGetCreatedAt(): Carbon {
        return $this->created_at ?? Carbon::now();
    }
}
