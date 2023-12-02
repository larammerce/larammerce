<?php

namespace App\Models;

use App\Utils\CRMManager\Enums\CRMPersonType;
use App\Utils\CRMManager\Interfaces\CRMAccountInterface;
use App\Utils\CRMManager\Interfaces\CRMLeadInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer id
 * @property integer customer_user_id
 * @property string company_name
 * @property string economical_code
 * @property string national_id
 * @property string registration_code
 * @property string company_phone
 * @property string fin_relation
 * @property integer state_id
 * @property integer city_id
 * @property string crm_account_id
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property CustomerUser customerUser
 * @property State state
 * @property City city
 *
 * Class CustomerUserLegalInfo
 * @package App\Models
 */
class CustomerUserLegalInfo extends BaseModel implements CRMAccountInterface {
    protected $table = 'customer_users_legal_info';
    public $timestamps = false;

    protected $fillable = [
        'customer_user_id', 'company_name', 'economical_code', 'national_id',
        'registration_code', 'company_phone', 'state_id', 'city_id', 'fin_relation', 'crm_account_id'
    ];

    protected static array $SORTABLE_FIELDS = ['id'];

    protected static array $SEARCHABLE_FIELDS = [
        'id',
        'company_name',
        'economical_code',
        'national_id',
        'registration_code',
        'company_phone',
        'fin_relation',
        'crm_lead_id'
    ];

    public function customerUser(): BelongsTo {
        return $this->belongsTo(CustomerUser::class, 'customer_user_id');
    }

    public function state(): BelongsTo {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city(): BelongsTo {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string {
        return '';
    }

    function crmGetFullName(): string {
        return $this->company_name;
    }

    function crmGetFirstName(): string {
        return $this->customerUser->user?->name ?? "";
    }

    function crmGetLastName(): string {
        return $this->customerUser->user?->family ?? "";
    }

    function crmGetSource(): string {
        return "Website registration";
    }

    function crmGetMainPhone(): string {
        return $this->company_phone ?? "";
    }

    function crmGetSecondaryPhone(): string {
        return $this->customerUser?->main_phone ?? "";
    }

    function crmHasSecondaryPhone(): bool {
        return true;
    }

    function crmGetEmail(): string {
        return $this->customerUser->user->email ?? "";
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

    function crmGetPersonType(): string {
        return CRMPersonType::CORPORATE;
    }
}
