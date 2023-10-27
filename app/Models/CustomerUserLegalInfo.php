<?php

namespace App\Models;

use App\Utils\CRMManager\Enums\CRMLeadType;
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
 * @property string crm_relation
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
class CustomerUserLegalInfo extends BaseModel implements CRMLeadInterface {
    protected $table = 'customer_users_legal_info';
    public $timestamps = false;

    protected $fillable = [
        'customer_user_id', 'company_name', 'economical_code', 'national_id',
        'registration_code', 'company_phone', 'state_id', 'city_id', 'fin_relation', 'crm_relation'
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
        'crm_relation'
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

    function leadGetRelation(): string {
        return $this->crm_relation ?? "";
    }

    function leadSetRelation(string $lead_id): void {
        $this->update([
            "crm_relation" => $lead_id
        ]);
    }

    function leadGetFullName(): string {
        return $this->company_name;
    }

    function leadGetFirstName(): string {
        return $this->customerUser->user?->name ?? "";
    }

    function leadGetLastName(): string {
        return $this->customerUser->user?->family ?? "";
    }

    function leadGetSource(): string {
        return "Website registration";
    }

    function leadGetMainPhone(): string {
        return $this->company_phone ?? "";
    }

    function leadGetSecondaryPhone(): string {
        return $this->customerUser?->main_phone ?? "";
    }

    function leadHasSecondaryPhone(): bool {
        return true;
    }

    function leadGetEmail(): string {
        return $this->customerUser->user->email ?? "";
    }

    function leadGetType(): string {
        return CRMLeadType::CORPORATE;
    }

    function leadGetCreatedAt(): Carbon {
        return $this->created_at ?? Carbon::now();
    }
}
