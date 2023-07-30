<?php

namespace App\Models;

use App\Enums\Product\ProductStatus;
use App\Enums\Setting\CMSSettingKey;
use App\Exceptions\Product\ProductPackageItemInvalidCountException;
use App\Exceptions\Product\ProductPackageItemInvalidIdException;
use App\Exceptions\Product\ProductPackageItemNotFoundException;
use App\Exceptions\Product\ProductPackageNotExistsException;
use App\Helpers\CMSSettingHelper;
use App\Interfaces\CMSExposedNodeInterface;
use App\Interfaces\HashInterface;
use App\Interfaces\ImageOwnerInterface;
use App\Interfaces\PublishScheduleInterface;
use App\Interfaces\RateOwnerInterface;
use App\Interfaces\SeoSubjectInterface;
use App\Interfaces\ShareSubjectInterface;
use App\Services\Directory\DirectoryLocationService;
use App\Services\Invoice\NewInvoiceService;
use App\Traits\Badgeable;
use App\Traits\Fileable;
use App\Traits\FullTextSearch;
use App\Traits\Rateable;
use App\Traits\Seoable;
use App\Utils\CMS\AdminRequestService;
use App\Utils\CMS\ProductService;
use App\Utils\CMS\Setting\CustomerLocation\CustomerLocationModel;
use App\Utils\Common\EmailService;
use App\Utils\Common\ImageService;
use App\Utils\Common\SMSService;
use App\Utils\FinancialManager\ConfigProvider;
use App\Utils\FinancialManager\Exceptions\FinancialDriverInvalidConfigurationException;
use App\Utils\FinancialManager\Factory;
use App\Utils\Translation\Traits\Translatable;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\QueryException;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;
use Throwable;

/**
 *
 * @property integer id
 * @property string title
 * @property integer latest_price
 * @property integer latest_special_price
 * @property integer previous_price
 * @property integer tax_amount
 * @property integer toll_amount
 * @property integer pure_price
 * @property string extra_properties
 * @property string description
 * @property string color_code
 * @property string code
 * @property integer directory_id
 * @property integer p_structure_id
 * @property DateTime created_at
 * @property DateTime updated_at
 * @property float average_rating
 * @property int rates_count
 * @property int count
 * @property boolean is_active
 * @property DateTime important_at
 * @property boolean is_important
 * @property boolean is_accessory
 * @property integer accessory_for
 * @property integer min_allowed_count
 * @property integer max_purchase_count
 * @property integer min_purchase_count
 * @property integer inaccessibility_type
 * @property string seo_title
 * @property string seo_keywords
 * @property string seo_description
 * @property bool isLiked
 * @property int model_id
 * @property bool has_discount TODO: rename this property to has_special_offer
 * @property bool is_visible
 * @property integer cmc_id
 * @property string notice
 * @property integer models_count
 * @property integer discount_group_id
 * @property integer latest_sell_price
 * @property boolean is_new
 * @property boolean is_discountable
 * @property string structure_sort_score
 * @property bool is_package
 * @property integer maximum_allowed_purchase_count
 * @property integer minimum_allowed_purchase_count
 *
 * @property CustomerLocationModel[] location_limitations
 * @property bool is_location_limited
 * @property bool can_deliver
 *
 * @property Directory directory
 * @property Directory[] directories
 * @property PStructure productStructure
 * @property Color[] colors
 * @property ProductPrice[] prices
 * @property ProductImage[] images
 * @property PStructureAttrKey[] attributeKeys
 * @property PStructureAttrValue[] attributeValues
 * @property PAttr[]|Collection pAttributes
 * @property Invoice[] invoices
 * @property CustomerUser[] wishLists
 * @property CustomerUser[] needLists
 * @property CartRow[] cartRows
 * @property Tag[] tags
 * @property Badge[] badges
 * @property Review review
 * @property CustomerMetaCategory customerMetaCategory
 * @property DiscountGroup discountGroup
 * @property integer priority
 * @property ProductPackage productPackage
 * @property ProductPackage[] productPackages
 *
 * @method static Product find(integer $id)
 * @method static Builder models(Product $product, Boolean $onlyOthers = true)
 *
 * Class Product
 * @package App\Models
 */
class Product extends BaseModel implements
    CMSExposedNodeInterface, ShareSubjectInterface, PublishScheduleInterface, ImageOwnerInterface,
    RateOwnerInterface, SeoSubjectInterface, HashInterface
{
    use Rateable, Seoable, Fileable, FullTextSearch, Badgeable, Translatable;

    public $timestamps = true;
    protected $appends = ["is_liked", "is_needed", "main_photo", "secondary_photo", "fin_man_price",
        "status", "url", "is_main_model", "minimum_allowed_purchase_count", "maximum_allowed_purchase_count",
        "is_new", "is_important", "location_limitations", "is_location_limited", "can_deliver"];
    protected $hidden = ["count", "min_allowed_count", "max_purchase_count"];
    protected $table = "products";
    protected $attributes = [
        "extra_properties" => "[]"
    ];


    protected $fillable = [
        "title", "latest_price", "latest_special_price", "extra_properties", "directory_id", "p_structure_id",
        "description", "code", "average_rating", "rates_count", "is_active",
        "min_allowed_count", "max_purchase_count", "min_purchase_count",
        "is_important", "seo_title", "seo_keywords", "seo_description", "model_id",
        "has_discount", "previous_price", "is_accessory", "is_visible", "inaccessibility_type",
        "cmc_id", "notice", "discount_group_id", "priority", "is_discountable", "structure_sort_score",
        "is_package", "accessory_for", "count",
        //these are not table fields, these are form sections that role permission system works with
        "tags", "attributes", "gallery", "colors", "badges"
    ];

    protected $casts = [
        "important_at" => "timestamp",
        "is_accessory" => "bool",
        "is_package" => "bool",
        "is_active" => "bool",
        "is_visible" => "bool",
        "is_discountable" => "bool"
    ];

    protected $with = [
        "discountGroup", "directory", "images"
    ];

    protected static ?bool $DISABLE_ON_MIN = null; //TODO: move this to admin layer setting.
    protected static array $SORTABLE_FIELDS = ["id", "created_at", "is_active", "is_accessory"];
    protected static int $FILTER_PAGINATION_COUNT = 20;
    protected static array $SEARCHABLE_FIELDS = ["seo_keywords", "title", "code", "description"];
    protected static ?string $IMPORTANT_SEARCH_FIELD = "title";
    protected static ?string $EXACT_SEARCH_ORDER_FIELD = "is_active";
    protected static array $ROLE_PROPERTY_ACCESS = [
        "super_user" => ["*"],
        "acc_manager" => ["*"],
        "stock_manager" => [
            "min_allowed_count",
            "max_purchase_count",
            "min_purchase_count",
            "is_important",
            "has_discount",
            "is_discountable"
        ],
        "cms_manager" => [
            "title", "description", "code", "is_active", "extra_properties", "gallery", "colors", "attributes", "tags",
            "is_important", "seo_title", "seo_keywords", "seo_description", "model_id", "has_discount", "is_accessory", "notice",
            "priority", "is_discountable", "badges", "accessory_for"
        ],
        "seo_master" => [
            "tags", "description", "seo_title", "seo_keywords", "seo_description"
        ]
    ];

    protected static array $IMPORTABLE_ATTRIBUTES = [
        "code" => "required|string",
        "latest_price" => "required|int",
        "count" => "required|int"
    ];

    protected static array $TRANSLATABLE_FIELDS = [
        "title" => ["string", "input:text"],
        "seo_title" => ["text", "textarea:normal"],
        "seo_keywords" => ["text", "textarea:normal"],
        "seo_description" => ["text", "textarea:normal"],
        "description" => ["text", "textarea:rich"],
        "extra_properties" => ["text", "json"]
    ];

    protected static string $TRANSLATION_EDIT_FORM = "admin.pages.product.translate";

    private CMSSettingHelper $setting_service;
    private NewInvoiceService $new_invoice_service;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->setting_service = app(CMSSettingHelper::class);
        $this->new_invoice_service = app(NewInvoiceService::class);
    }

    public function getIsLocationLimitedAttribute(): bool {
        if (config("cms.general.site.enable_directory_location")) {
            return DirectoryLocationService::isProductLocationLimited($this);
        }
        return false;
    }

    public function getLocationLimitationsAttribute(): array {
        if (config("cms.general.site.enable_directory_location")) {
            return DirectoryLocationService::getProductLocationLimitations($this);
        }
        return [];
    }

    public function getCanDeliverAttribute(): bool {
        if (config("cms.general.site.enable_directory_location")) {
            return DirectoryLocationService::canDeliverProduct($this);
        }
        return true;
    }

    public function getIsLikedAttribute(): bool {
        return is_customer() and
            get_customer_user()->wishList()->where("product_id", $this->id)->count() > 0;
    }

    public function getIsNeededAttribute(): bool {
        return is_customer() and get_customer_user() != false and
            get_customer_user()->needList()->where("product_id", $this->id)->count() > 0;
    }

    public function getIsNewAttribute(): bool {
        if ($this->created_at === null)
            return false;
        $new_product_delay_days = $this->setting_service->getCMSSettingAsInt(CMSSettingKey::NEW_PRODUCT_DELAY_DAYS);
        return Carbon::now()->lessThan($this->created_at->addDays($new_product_delay_days));
    }

    public function getIsImportantAttribute(): bool {
        return isset($this->attributes["important_at"]) and $this->attributes["important_at"] !== null;
    }

    public function setIsImportantAttribute($value): void {
        if ($value) {
            $this->attributes["important_at"] = Carbon::now();
        } else {
            $this->attributes["important_at"] = null;
        }
    }

    public function setPStructureIdAttribute(int $p_structure_id): void {
        try {
            $p_structure = PStructure::findOrFail($p_structure_id);
            $this->pAttributes()->whereNotIn("p_structure_attr_key_id",
                $p_structure->attributeKeys()->pluck("p_structure_attr_key_id")->toArray())->delete();
            $this->attributes["p_structure_id"] = $p_structure_id;
        } catch (Exception $e) {
            return;
        }
    }

    public function getMainPhotoAttribute(): string {
        return ImageService::getImage($this, "preview");
    }

    public function getSecondaryPhotoAttribute(): string {
        return ImageService::getImage($this->getSecondaryPhoto(), "preview");
    }

    public function getFinManPriceAttribute(): int {
        return $this->getStandardLatestPrice();
    }

    public function getStatusAttribute(): string {
        return $this->is_active ? "active" : "not-active";
    }

    public function getUrlAttribute(): string {
        return $this->getFrontUrl();
    }

    public function getIsMainModelAttribute(): bool {
        return $this->isMainModel();
    }

    public function getMinimumAllowedPurchaseCountAttribute(): int {
        return $this->getMinimumAllowedPurchaseCount();
    }

    public function getMaximumAllowedPurchaseCountAttribute(): int {
        return $this->getMaximumAllowedPurchaseCount();
    }

    public function getHasDiscountAttribute(): bool {
        try {
            return (!AdminRequestService::isInAdminArea() and ($this->attributes["has_discount"] ?? false) and
                    (($this->attributes["latest_special_price"] != 0) or
                        ($this->is_package and $this->productPackage->getLatestSpecialPrice() != 0))) or
                (AdminRequestService::isInAdminArea() and ($this->attributes["has_discount"] ?? false));
        } catch (Exception $e) {
            return false;
        }
    }

    public function getLatestPriceAttribute(): int {
        try {
            if ($this->has_discount and !AdminRequestService::isInAdminArea())
                return ($this->is_package and $this->attributes["latest_price"] == 0) ?
                    $this->productPackage->getLatestSpecialPrice() : $this->attributes["latest_special_price"];
            else
                return ($this->is_package and $this->attributes["latest_price"] == 0) ?
                    $this->productPackage->getLatestPrice() : $this->attributes["latest_price"];
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getPurePriceAttribute(): int {
        try {
            return ($this->is_package and $this->attributes["pure_price"] == 0) ?
                $this->productPackage->getPurePrice() : $this->attributes["pure_price"];
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getTaxAmountAttribute(): int {
        try {
            return ($this->is_package and $this->attributes["tax_amount"] == 0) ?
                $this->productPackage->getTaxAmount() : $this->attributes["tax_amount"];
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getTollAmountAttribute(): int {
        try {
            return ($this->is_package and $this->attributes["toll_amount"] == 0) ?
                $this->productPackage->getTollAmount() : $this->attributes["toll_amount"];
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getPreviousPriceAttribute(): int {
        if ($this->has_discount and !AdminRequestService::isInAdminArea())
            return $this->is_package ? $this->productPackage->getLatestPrice() :
                $this->attributes["latest_price"];
        else
            return $this->is_package ? $this->productPackage->getPreviousPrice() :
                $this->attributes["previous_price"];
    }

    public function getLatestSellPriceAttribute(): int {
        if ($this->is_package) {
            $product_package = $this->productPackage;
            if (
                isset($this->attributes["has_discount"]) and
                $this->attributes["has_discount"] and
                $product_package->getLatestSpecialPrice() != 0
            )
                return $product_package->getLatestSpecialPrice();
            return $product_package->getLatestPrice();
        } else
            return (
                isset($this->attributes["has_discount"]) and
                $this->attributes["has_discount"] and
                $this->attributes["latest_special_price"] != 0
            ) ?
                $this->attributes["latest_special_price"] :
                $this->attributes["latest_price"];
    }

    public function getCountAttribute(): int {
        return $this->is_package ? ($this->productPackage?->getCount() ?? 0) :
            ($this->attributes["count"] ?? 0);
    }

    public function setModelIdAttribute($value) {
        $this->attributes["model_id"] = $value ?: $this->id;
    }

    public function setExtraPropertiesAttribute(?array $extra_properties) {
        $this->attributes["extra_properties"] = json_encode(array_filter($extra_properties,
            function ($iter_property) {
                return $iter_property["key"] !== null;
            }) ?? []);
    }

    public function getExtraProperties() {
        return json_decode($this->extra_properties);
    }

    public function directory(): BelongsTo {
        return $this->belongsTo(Directory::class, "directory_id", "id");
    }

    public function directoryLocations(): HasMany {
        return $this->hasMany(DirectoryLocation::class, "directory_id", "directory_id");
    }

    public function directories(): BelongsToMany {
        return $this->belongsToMany(Directory::class, "directory_product", "product_id", "directory_id");
    }

    public function productStructure(): BelongsTo {
        return $this->belongsTo(PStructure::class, "p_structure_id", "id");
    }

    public function colors(): BelongsToMany {
        return $this->belongsToMany(Color::class, "product_color", "product_id", "color_id");
    }

    public function prices(): HasMany {
        return $this->hasMany(ProductPrice::class, "product_id", "id");
    }

    public function specialPrices(): HasMany {
        return $this->hasMany(ProductSpecialPrice::class, "product_id", "id");
    }

    public function images(): HasMany {
        return $this->hasMany(ProductImage::class, "product_id");
    }

    public function attributeKeys(): BelongsToMany {
        return $this->belongsToMany(PStructureAttrKey::class, "p_attr_assignments",
            "product_id", "p_structure_attr_key_id")->distinct("id");
    }

    public function attributeValues(): BelongsToMany {
        return $this->belongsToMany(PStructureAttrValue::class, "p_attr_assignments",
            "product_id", "p_structure_attr_value_id");
    }

    public function invoices(): BelongsToMany {
        return $this->belongsToMany(Product::class, "invoice_rows", "product_id", "invoice_id");
    }

    public function wishLists(): BelongsToMany {
        return $this->belongsToMany(CustomerUser::class, "customer_wish_lists", "product_id", "customer_user_id");
    }

    public function needLists(): BelongsToMany {
        return $this->belongsToMany(CustomerUser::class, "customer_need_lists",
            "product_id", "customer_user_id")->withTimestamps();
    }

    public function cartRows(): HasMany {
        return $this->hasMany(CartRow::class, "product_id");
    }

    public function tags(): MorphToMany {
        return $this->morphToMany(Tag::class, "taggable");
    }

    public function pAttributes(): HasMany {
        return $this->hasMany(PAttr::class, "product_id", "id");
    }

    public function invoiceRows(): HasMany {
        return $this->hasMany(InvoiceRow::class, "product_id", "id");
    }

    public function customerMetaCategory(): BelongsTo {
        return $this->belongsTo(CustomerMetaCategory::class, "cmc_id", "id");
    }

    public function discountGroup(): BelongsTo {
        return $this->belongsTo(DiscountGroup::class, "discount_group_id", "id")
            ->whereRaw(DB::raw($this->has_discount ? "1=0" : "1=1"))
            ->where("is_active", true)->where(function ($query) {
                $query->where("has_expiration", false)->orWhere("expiration_date", ">=", Carbon::now());
            });
    }

    public function productPackage(): HasOne {
        return $this->hasOne(ProductPackage::class, 'product_id');
    }

    public function productPackages(): BelongsToMany {
        return $this->belongsToMany(ProductPackage::class,
            "product_package_items", "product_id", "package_id");
    }

    public function productPackageItems(): HasMany {
        return $this->hasMany(ProductPackageItem::class, "product_id");
    }

    public function getMainProduct(): ?Product {
        if ($this->model_id === $this->id)
            return $this;
        return Product::find($this->model_id);
    }

    public function accessories(): Builder {
        return static::where("is_accessory", true)->where("accessory_for", $this->model_id);
    }

    public function accessoryFor(): Builder {
        return static::where("is_accessory", false)->where("model_id", $this->accessory_for);
    }

    /**
     * @param mixed $package_items
     * @throws ProductPackageItemNotFoundException
     * @throws ProductPackageItemInvalidCountException
     * @throws ProductPackageItemInvalidIdException
     * @throws ProductPackageNotExistsException
     */
    public function syncPackageItems($package_items) {
        $productPackage = $this->productPackage;
        if ($productPackage != null) {
            $items_to_attach = collect();
            foreach ($package_items as $item) {
                if (isset($item["product_id"]) and $item["product_id"] != null
                    and $item["product_id"] != "") {
                    $id = $item["product_id"];
                    if (Product::find($id)->exists()) {
                        $package_item = new stdClass();
                        $package_item->product_id = $id;
                        if (isset($item["product_count"]) and
                            $item["product_count"] != null and
                            is_numeric($item["product_count"])) {
                            $package_item->product_count = $item["product_count"];
                            $items_to_attach->push($package_item);
                        } else
                            throw new ProductPackageItemInvalidCountException();
                    } else {
                        throw new ProductPackageItemInvalidIdException();
                    }
                }
            }
            $productPackage->products()->sync($items_to_attach->pluck('product_id')->toArray());
            foreach ($items_to_attach as $package_item)
                $productPackage->setProductUsageCount($package_item->product_count, $package_item->product_id);
        } else {
            throw new ProductPackageNotExistsException();
        }
    }

    public function scopeLatest(Builder $query): Builder {
        return $query->orderBy("id", "DESC");
    }

    public function scopeExcept(Builder $query, $id): Builder {
        return $query->where("id", "!=", $id);
    }

    public function scopeMainModels(Builder $query): Builder {
        return $query->groupBy(["color_code", "model_id", "is_active"]);
    }

    public function scopeModels(Builder $query, Product $product, bool $onlyOthers = true): Builder {
        $result = $query->whereNotNull("model_id")
            ->where("model_id", "=", $product->model_id);
        if ($onlyOthers)
            return $result->where("id", "!=", $product->id);
        return $result;
    }

    public function scopeImportant(Builder $query): Builder {
        return $query->where("important_at", '!=', null);
    }

    public function scopeVisible(Builder $query): Builder {
        return $query->where("is_visible", true);
    }

    public function scopeIsActive(Builder $query): Builder {
        if (config("cms.general.site.show_deactivated_products")) {
            return $query->where("latest_price", ">", "0");
        } else {
            return $query->where('is_active', true);
        }
    }

    public function scopeHasDiscount(Builder $query): Builder {
        return $query->where("has_discount", true);
    }

    public function delete() {
        $this->rates()->delete();
        $this->tags()->detach();
        $this->review()->delete();
        $this->badges()->detach();

        return parent::delete();
    }

    public function save(array $options = []) {
        //TODO: This method content should be moved to accessor methods.

        $this->updateEnabledStatus(false);
        $this->color_code = $this->generateColorCode();
        $this->code = drop_non_ascii($this->code);

        if (strlen($this->extra_properties) == 0) {
            $this->extra_properties = "[]";
        }

        if ($this->id and $this->isDirty("latest_price") and $this->attributes["latest_price"] !== $this->attributes["previous_price"]) {
            $this->previous_price = $this->original["latest_price"] ?? 0;
            $this->prices()->create(["value" => $this->latest_price]);
        }

        if ($this->id and $this->isDirty("latest_special_price")) {
            $this->specialPrices()->create(["value" => $this->latest_special_price]);
        }

        if ($this->id and ($this->isDirty("latest_special_price") or $this->isDirty("latest_price"))) {
            $this->updateTaxAmount();
        }

        $result = parent::save($options);
        if (!$this->isDirty(["models_count"]))
            $this->updateModelsCount();

        //Call UpdateEnabledStatus() for packages having this product
        if (!$this->is_package)
            foreach ($this->productPackages as $product_package)
                $product_package->product->save();

        return $result;
    }

    public function update(array $attributes = [], array $options = []): bool {
        try {
            return parent::update($attributes, $options);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), "products_code_unique")) {
                $this->code = "dup_" . $this->code;
                return parent::update($attributes, $options);
            }
            throw $e;
        }
    }

    public function generateColorCode(): string {
        $result = "";
        foreach ($this->colors()->orderBy("id")->get() as $color) {
            $result .= "#{$color->id}";
        }
        $result .= "#";
        return $result;
    }

    public function updateTaxAmount(): void {

        $priceData = ConfigProvider::isTaxAddedToPrice() ?
            $this->new_invoice_service->reverseCalculateProductTaxAndToll(
                intval($this->latest_sell_price / $this->new_invoice_service->getProductPriceRatio())
            ) : $this->new_invoice_service->calculateProductTaxAndToll(
                $this->latest_sell_price / $this->new_invoice_service->getProductPriceRatio()
            );

        $this->pure_price = $priceData->price;
        $this->tax_amount = $priceData->tax;
        $this->toll_amount = $priceData->toll;
    }

    public function updateFinData(): bool {
        try {
            $std_product = Factory::driver()->getProduct($this->code);
        } catch (FinancialDriverInvalidConfigurationException $e) {
            $std_product = false;
        }

        if ($std_product === false) {
            Log::error("product.updater.$this->id : can not fetch product stock data from fin man server " .
                $this->code);
            $this->makeDisabled();
            return false;
        } else if ($std_product === true) {
            return true; //state for fin-man local.
        } else {
            $this->count = $std_product->count;
            $this->latest_price = $std_product->price;
            try {
                $this->save();
                return true;
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return false;
            }
        }
    }

    public function buildStructureSortScore(PStructureAttrKey $key): bool {
        $p_attr = DB::table(DB::raw('p_attr_assignments as paa1'))
            ->where('paa1.p_structure_attr_key_id', $key->id)
            ->where('paa1.product_id', $this->id)
            ->join('p_structure_attr_values as psav1', 'paa1.p_structure_attr_value_id', '=', 'psav1.id')
            ->select(DB::raw("*, sum(psav1.priority) as sort_score"))
            ->first();
        if ($p_attr != null and isset($p_attr->sort_score))
            $this->structure_sort_score = $p_attr->sort_score;
        return $this->save();
    }

    private function updateEnabledStatus(bool $do_save = true): bool {
        $min_allowed_count = $this->setting_service->getCMSSettingAsBool(CMSSettingKey::DISABLE_PRODUCT_ON_MIN) ?
            $this->min_allowed_count : 0;
        if ($this->count > $min_allowed_count and $this->latest_price > 0)
            return $this->makeEnabled($do_save);
        return $this->makeDisabled($do_save);
    }

    private function makeDisabled(bool $do_save = true): bool {
        if ($this->is_active) {
            $this->is_active = false;
            try {
                if ($do_save)
                    $this->save();
            } catch (Exception $saveException) {
                Log::error("product.makeDisabled.$this->id : can not save the product changes");
                return false;
            }
            Log::info("product.notification.$this->id : product is ran out. (to stock managers)");
            $this->deleteFromCarts();
            $this->sendNotificationToStockManagers(ProductStatus::Disabled);
        }
        return true;
    }

    private function makeEnabled(bool $do_save = true): bool {
        if (!$this->is_active) {
            $this->is_active = true;
            try {
                if ($do_save)
                    $this->save();
            } catch (Exception $saveException) {
                Log::error("product.makeEnabled.$this->id : can not save the product changes");
                return false;
            }
            $this->sendNotificationToStockManagers(ProductStatus::Enabled);

            //TODO: change notify system for all notifications
            foreach ($this->needLists as $customerUser) {
                $this->needLists()->detach($customerUser->id);
                SMSService::send("sms-need-list-available", $customerUser->main_phone, [], [
                    "customerName" => $customerUser->user->name,
                    "productTitle" => $this->title,
                ]);
            }
            Log::info("product.notification.$this->id : product now is available. (to customers and stock managers)");
        }
        return true;
    }

    private function deleteFromCarts(): void {
        $this->cartRows()->delete();
        Log::info("product.notification.$this->id : product ran out. (to customers)");
        //TODO: notify customer about that
    }

    private function sendNotificationToStockManagers(int $status): void {
        if (config("cms.general.site.stock_manager_notification") === false)
            return;

        $stockManagers = SystemUser::where("is_stock_manager", true)->get();
        if (count(is_countable($stockManagers) ? $stockManagers : []) > 0) {
            $productTitle = $this->title;
            $productDirectoryTitle = $this->directory->title;
            $adminUrl = $this->directory->getAdminUrl();
            foreach ($stockManagers as $stockManager) {
                if ($stockManager->user != null and $stockManager->user->email != null
                    and $adminUrl != null and $productTitle != null and $productDirectoryTitle != null) {

                    $template = null;
                    $subject = null;

                    if ($status == ProductStatus::Disabled) {
                        $template = "public.mail-product-make-disable";
                        $subject = "ناموجود شدن محصول";
                    } elseif ($status == ProductStatus::Enabled) {
                        $template = "public.mail-product-make-enable";
                        $subject = "موجود شدن محصول";
                    }

                    if ($template != null and $subject != null) {
                        EmailService::send([
                            "productTitle" => $productTitle,
                            "productDirectoryTitle" => $productDirectoryTitle,
                            "adminUrl" => $adminUrl,
                        ],
                            $template,
                            $stockManager->user->email,
                            null,
                            "$subject"
                        );
                    }
                }
            }
        }
    }

    public function hasImage(): bool {
        if (isset($this->relations["images"])) {
            return count($this->images) > 0;
        }
        return $this->images()->main()->count() > 0;
    }

    public function getImagePath(): string {
        if (isset($this->relations["images"])) {
            foreach ($this->images as $image) {
                if ($image->is_main)
                    return $image->getImagePath();
            }
            return $this->getDefaultImagePath();
        }
        return $this->images()->main()->first()->getImagePath();
    }

    public function setImagePath(): void {
        // the process is in handled in ProductImage model
    }

    public function removeImage(): void {
        // the process is in handled in ProductImage model
    }

    public function getDefaultImagePath(): string {
        return "/admin_dashboard/images/No_image.jpg.png";
    }

    public function getImageCategoryName(): string {
        return "product";
    }

    public function getMainPhoto(): ?ProductImage {
        if (isset($this->relations["images"])) {
            foreach ($this->images as $image) {
                if ($image->is_main)
                    return $image;
            }
            return null;
        }
        return $this->images()->main()->first();
    }

    public function getSecondaryPhoto(): ?ProductImage {
        if (isset($this->relations["images"])) {
            foreach ($this->images as $image) {
                if ($image->is_secondary)
                    return $image;
            }
            return null;
        }
        return $this->images()->secondary()->first();
    }

    public function parentField(): string {
        return "directory_id";
    }

    public function getName(): string {
        return $this->title;
    }

    public function getAdminUrl(): string {
        try {
            return route("admin.product.show", $this);
        } catch (UrlGenerationException $e) {
            return '';
        }
    }

    public function hasValue($value): bool {
        return $this->pAttributes->contains(function ($v, $k) use ($value) {
            return $v->p_structure_attr_value_id == $value->id;
        });
    }

    public static function getFilterPaginationCount(): int {
        if (request()->has("pagination_count"))
            return request("pagination_count");
        return self::$FILTER_PAGINATION_COUNT;
    }

    public function getFrontUrl(): string {
        try {
            return lm_route("public.view-product", $this) . "/" . url_encode($this->title);
        } catch (UrlGenerationException $e) {
            return '';
        }
    }

    // Todo : this method must be checked check
    public function getMaximumAllowedPurchaseCount() {
        try {
            $min_allowed_count = $this->setting_service->getCMSSettingAsBool(CMSSettingKey::DISABLE_PRODUCT_ON_MIN) ?
                $this->min_allowed_count : 0;
            return max((config('cms.general.site.show_deactivated_products') ? 1 : 0),
                min(($this->count - $min_allowed_count), $this->max_purchase_count));
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getMinimumAllowedPurchaseCount(): int {
        return $this->min_purchase_count ?: 1;
    }

    public function getSearchUrl(): string {
        try {
            return route("admin.product.edit", $this);
        } catch (UrlGenerationException $e) {
            return '';
        }
    }

    public function getStandardLatestPrice(): int {
        return intval($this->latest_price / $this->new_invoice_service->getProductPriceRatio());
    }

    public function isMainModel(): bool {
        return $this->model_id == null or $this->model_id == $this->id;
    }

    public function getModels(array $with_relations = []): Collection|array {
        return Product::models($this, true)->with($with_relations)->get();
    }

    public function getPAttributes($show_type): array {
        $result = [];
        $pAttributes = $this->pAttributes()->whereHas("key", function ($q) use ($show_type) {
            $q->where("show_type", $show_type);
        })->with("key", "value")->get();
        foreach ($pAttributes as $attribute) {
            $keyId = $attribute->p_structure_attr_key_id;
            if (isset($result[$keyId])) {
                $result[$keyId]->values[] = $attribute->value;
            } else {
                $result[$keyId] = new stdClass();
                $result[$keyId]->key = $attribute->key;
                $result[$keyId]->values = [$attribute->value];
            }
        }
        return $result;
    }


    /**
     * @return string
     */
    function getTitle(): string {
        return $this->title;
    }

    public function getSeoTitle() {
        if ($this->seo_title !== null and strlen($this->seo_title) > 0)
            return $this->seo_title;
        return $this->title . " - " . $this->directory->title;
    }


    public function getSeoDescription() {
        return $this->seo_description;
    }

    public function getSeoKeywords() {
        return $this->seo_keywords;
    }

    public function attachFileTo(?Directory $dest): void {
        $this->directory_id = $dest?->id;
        $this->save();
        $dest?->attachLeafFiles($this->id);
    }

    /**
     * fill $dest param to detach more efficiently.
     * @param Directory|null $dest
     * @return mixed
     */
    public function detachFile($dest = null) {
        if ($this->directory != null) {
            $destParentDirectoriesIds = [];
            if ($dest != null) {
                $destParentDirectories = collect();
                if ($dest->directory_id != null) {
                    $destParentDirectory = $dest->parentDirectory;
                    while ($destParentDirectory != null) {
                        $destParentDirectories->push($destParentDirectory);
                        $destParentDirectory = $destParentDirectory->parentDirectory;
                    }
                }
                $destParentDirectoriesIds = $destParentDirectories->pluck("id")->toArray();
            }

            $parent = $this->directory;
            while ($parent != null and !in_array($parent->id, $destParentDirectoriesIds)) {
                $parent->leafProducts()->detach($this->id);
                $parent = $parent->parentDirectory;
            }
        }
    }

    /**
     * @return Model|CMSExposedNodeInterface
     */
    public function cloneFile() {
        if ($this->model_id == null)
            $this->update(["model_id" => $this->id]);

        $newProduct = $this->replicate(['code']);
        $newProduct->code = null;
        $newProduct->push();
        $newProduct->createReview();

        foreach ($this->colors as $color)
            $newProduct->colors()->attach($color->id);

        foreach ($this->pAttributes as $pAttribute) {
            $newProductAttribute = $pAttribute->replicate();
            $newProductAttribute->product_id = $newProduct->id;
            $newProductAttribute->push();
        }
        return $newProduct;
    }

    /**
     * @param $dest
     * @return void
     * @throws Throwable
     */
    public function generateNewUrls($dest) {
        // TODO: Implement generateNewUrls() method.
        $this->save();
    }

    public function getHash() {
        return md5($this->title . "#" .
            ($this->latest_price != null ? $this->latest_price : 0) . "#" .
            $this->extra_properties . "#" .
            $this->p_structure_id . "#" .
            $this->description . "#" .
            $this->code . "#" .
            ($this->average_rating != null ? $this->average_rating : 0) . "#" .
            ($this->rates_count != null ? $this->rates_count : 0) . "#" .
            ($this->is_active != null ? $this->is_active : 0) . "#" .
            ($this->is_important != null ? $this->is_important : 0) . "#" .
            $this->seo_title . "#" .
            $this->seo_keywords . "#" .
            $this->seo_description . "#" .
            ($this->has_discount != null ? $this->has_discount : 0) . "#" .
            ($this->previous_price != null ? $this->previous_price : 0) . "#"
        );
    }

    public function isImageLocal() {
        return true;
    }

    /**
     * 1. Adds the public count if product setting is set to disable_on_min_allowed_count and
     *    current count is less than min_allowed_count.
     *
     * @return array
     */
    public function toArray(): array {
        $parent_result = parent::toArray();

        if (
            (!$this->setting_service->getCMSSettingAsBool(CMSSettingKey::DISABLE_PRODUCT_ON_MIN)) and
            $this->is_active and
            $this->count <= $this->min_allowed_count
        ) {
            $parent_result["public_count"] = $this->count;
        }

        return $parent_result;
    }

    public static function create(array $attributes = []) {
        if (!isset($attributes["cmc_id"]) and isset($attributes["directory_id"])) {
            $parent_directory = Directory::find($attributes["directory_id"]);
            if ($parent_directory !== null and $parent_directory->hasCustomerMetaCategory()) {
                $attributes["cmc_id"] = $parent_directory->cmc_id;
            }
        }
        return static::query()->create($attributes);
    }

    public function hasCustomerMetaCategory(): bool {
        return $this->cmc_id !== null;
    }

    private function updateModelsCount() {
        $models = Product::models($this, false)->get();
        $count = count($models);
        foreach ($models as $model) {
            if ($model->models_count !== $count) {
                $model->models_count = $count;
                $model->save();
            }
        }
    }
}
