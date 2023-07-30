<?php

namespace App\Models;

use App\Interfaces\ImageOwnerInterface;
use App\Interfaces\TagInterface as TaggableContract;
use App\Traits\Taggable;
use App\Utils\CMS\Enums\UserHome;
use App\Utils\Common\ImageService;
use App\Utils\FinancialManager\Exceptions\FinancialDriverInvalidConfigurationException;
use App\Utils\FinancialManager\Factory as FinFactory;
use DateTime;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Redis;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property integer id
 * @property string name
 * @property string family
 * @property string full_name
 * @property string username
 * @property string email
 * @property string password
 * @property string birthday_str
 * @property string image_path
 * @property integer gender
 * @property boolean is_system_user
 * @property boolean is_customer_user
 * @property DateTime created_at
 * @property DateTime updated_at
 * @property DateTime birthday
 * @property boolean is_email_confirmed
 * @property string representative_username
 * @property string representative_type
 *
 * @property SystemUser systemUser
 * @property CustomerUser customerUser
 * @property Setting[] settings
 *
 * @method static User find(integer $id)
 *
 * Class User
 * @package App\Models
 */
class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    ImageOwnerInterface,
    TaggableContract,
    JWTSubject
{
    use  Taggable, Authenticatable, Authorizable, CanResetPassword;

    protected $table = 'users';
    protected $with = ['systemUser', 'customerUser'];
    protected $appends = ['full_name'];
    public $timestamps = true;

    protected $fillable = [
        'name', 'family', 'username', 'email', 'password', 'is_system_user', 'is_customer_user', 'created_at',
        'updated_at', 'gender', 'birthday', 'birthday_str', 'is_email_confirmed', 'representative_username', 'representative_type'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'name', 'family', 'username', 'email', 'created_at'];

    protected static array $SEARCHABLE_FIELDS = [
        'id',
        'name',
        'family',
        'email',
        'username',
        'representative_username',
        'representative_type'
    ];

    protected $casts = [
        "is_system_user" => "bool",
        "is_customer_user" => "bool",
        "is_email_confirmed" => "bool"
    ];

    protected static array $EXPORTABLE_RELATIONS = [
        SystemUser::class => [
            "name" => 'systemUser'
        ],
        CustomerUser::class => [
            "name" => 'customerUser'
        ]
    ];

    public function systemUser(): HasOne {
        return $this->hasOne(SystemUser::class, 'user_id');
    }

    public function customerUser(): HasOne {
        return $this->hasOne(CustomerUser::class, 'user_id');
    }

    public function settings(): HasMany {
        return $this->hasMany(Setting::class, 'user_id');
    }

    public function scopeCustomerUsers($query) {
        return $query->where("is_customer_user", true);
    }

    public function scopeSystemUsers($query) {
        return $query->where("is_system_user", true);
    }

    public function getFullNameAttribute(): string {
        return $this->name . " " . $this->family;
    }

    /*
     * Image Methods
     */
    public function hasImage(): bool {
        return isset($this->image_path);
    }

    public function getImagePath(): ?string {
        return $this->image_path;
    }

    public function setImagePath() {
        $tmpImage = ImageService::saveImage($this->getImageCategoryName());
        $this->image_path = $tmpImage->destinationPath . '/' . $tmpImage->name;
        $this->save();
    }

    public function removeImage() {
        $this->image_path = null;
        $this->save();
    }

    public function getDefaultImagePath(): string {
        return '/admin_dashboard/images/No_image.jpg.png';
    }

    public function getImageCategoryName(): string {
        return 'profile';
    }

    public function getHomePath(): string {
        return $this->is_system_user ? UserHome::ADMIN_HOME : UserHome::CUSTOMER_HOME;
    }

    public static function getEloquentObject(AuthenticatableContract $genericUser): User {
        return self::find($genericUser->id);
    }

    public function save(array $options = []): bool {
        //before save
        $this->email = (is_string($this->email) and strlen($this->email) > 0) ? $this->email : null;

        $result = parent::save($options);

        //after save
        if ($result) {

            if (!$this->is_customer_user) {
                $this->customerUser?->delete();
            }

            if ($this->is_system_user) {
                if ($this->systemUser == null) {
                    SystemUser::create([
                        'user_id' => $this->id
                    ]);
                }
            } else {
                if ($this->systemUser) {
                    $this->systemUser->delete();
                }
            }
        }

        return $result;
    }

    public function hasAuthConfirmed() {
        return Redis::get("user:auth_confirm:id:" . $this->id);
    }

    public function setAuthConfirmed() {
        Redis::set("user:auth_confirm:id:{$this->id}", true, 'EX', 60 * 10);
    }

    private function getBirthdayItem($index): ?string {
        if ($this->birthday_str == null)
            return null;
        return explode('/', $this->birthday_str)[$index];
    }

    public function getBirthdayYear(): ?string {
        return $this->getBirthdayItem(0);
    }

    public function getBirthdayMonth(): ?string {
        return $this->getBirthdayItem(1);
    }

    public function getBirthdayDay(): ?string {
        return $this->getBirthdayItem(2);
    }

    public function hasSetPassword(): bool {
        return $this->password != "";
    }

    public function hasEmail(): bool {
        return isset($this->email) and is_string($this->email) and (strlen($this->email) > 0);
    }

    public function getText(): string {
        return $this->full_name;
    }

    public function getValue(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string {
        return route('admin.user.edit', $this);
    }

    /**
     * Financial Manager Methods
     * @throws FinancialDriverInvalidConfigurationException
     */
    public function saveFinManCustomer(): bool {
        if (!$this->relationLoaded('customerUser'))
            $this->load('customerUser');
        $result = FinFactory::driver()->addCustomer($this, false);
        if ($result === false)
            return false;
        $this->customerUser->update(["fin_relation" => $result, "is_active" => true]);
        return true;
    }

    /**
     * @throws FinancialDriverInvalidConfigurationException
     */
    public function updateFinManCustomer(array $config = []): bool {
        return FinFactory::driver()->editCustomer($this, false, $config);
    }

    /**
     * @throws FinancialDriverInvalidConfigurationException
     */
    public function saveFinManLegalCustomer(): bool {
        $result = FinFactory::driver()->addCustomer($this, true);
        if ($result === false)
            return false;
        $this->customerUser->legalInfo->fill(["fin_relation" => $result]);
        return true;
    }

    /**
     * @throws FinancialDriverInvalidConfigurationException
     */
    public function updateFinManLegalCustomer(array $user_config = []): bool {
        return FinFactory::driver()->editCustomer($this, true, $user_config);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier(): mixed {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array {
        return [];
    }

    public function isImageLocal(): bool {
        return true;
    }
}
