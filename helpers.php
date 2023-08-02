<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 10/9/17
 * Time: 2:02 PM
 */

use App\Enums\Directory\DirectoryType;
use App\Enums\Invoice\PaymentStatus;
use App\Enums\Setting\CMSSettingKey;
use App\Helpers\CMSSettingHelper;
use App\Models\Article;
use App\Models\BaseModel;
use App\Models\City;
use App\Models\Color;
use App\Models\CustomerAddress;
use App\Models\CustomerMetaCategory;
use App\Models\CustomerUser;
use App\Models\CustomerUserLegalInfo;
use App\Models\Directory;
use App\Models\District;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\Invoice;
use App\Models\PAttr;
use App\Models\Product;
use App\Models\ProductFilter;
use App\Models\ProductQuery;
use App\Models\PStructureAttrKey;
use App\Models\PStructureAttrValue;
use App\Models\State;
use App\Models\SystemUser;
use App\Models\User;
use App\Models\WebPage;
use App\Models\WPPost;
use App\Services\Invoice\NewInvoiceService;
use App\Utils\CMS\Cart\Provider as CartProvider;
use App\Utils\CMS\File\ClipBoardService;
use App\Utils\CMS\File\EmptyClipBoardException;
use App\Utils\CMS\File\InvalidTypeException;
use App\Utils\CMS\Platform\DetectService;
use App\Utils\CMS\Platform\OSType;
use App\Utils\CMS\Setting\Logistic\LogisticService;
use App\Utils\CMS\Setting\ShipmentCost\ShipmentCostService;
use App\Utils\CMS\SystemMessageService;
use App\Utils\FinancialManager\ConfigProvider;
use App\Utils\Jalali\JDateTime;
use App\Utils\PaymentManager\Provider;
use App\Utils\Reflection\ReflectiveNamespace;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

if (!function_exists("unparse_url")) {
    function unparse_url(array $parsed_url, array $ommit = []): string {
        $url = '';

        $p = array();

        $p['scheme'] = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';

        $p['host'] = $parsed_url['host'] ?? '';

        $p['port'] = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';

        $p['user'] = $parsed_url['user'] ?? '';

        $p['pass'] = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';

        $p['pass'] = ($p['user'] || $p['pass']) ? $p['pass'] . "@" : '';

        $p['path'] = $parsed_url['path'] ?? '';

        $p['query'] = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';

        $p['fragment'] = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

        if ($ommit) {
            foreach ($ommit as $key) {
                if (isset($p[$key])) {
                    $p[$key] = '';
                }
            }
        }

        return $p['scheme'] . $p['user'] . $p['pass'] . $p['host'] . $p['port'] . $p['path'] . $p['query'] . $p['fragment'];
    }
}

if (!function_exists("get_all_extras_percentage")) {
    function get_product_all_extras_percentage(): float {
        /** @var NewInvoiceService $new_invoice_service */
        $new_invoice_service = app(NewInvoiceService::class);
        return $new_invoice_service->getProductAllExtrasPercentage();
    }
}

if (!function_exists("locale_url")) {
    function locale_url(string $normal_url): string {
        if (count(config("translation.locales")) <= 1)
            return $normal_url;

        $parsed_url = parse_url($normal_url);
        $parsed_url["path"] = "/" . app()->getLocale() . ($parsed_url["path"] ?? "");
        return unparse_url($parsed_url);
    }
}

if (!function_exists("lm_route")) {
    function lm_route($name, $parameters = [], $absolute = true): string {
        $route_result = route($name, $parameters, $absolute);
        return locale_url($route_result);
    }
}

if (!function_exists("lm_url")) {
    function lm_url($path = null, $parameters = [], $secure = null): string {
        $url_result = url($path, $parameters, $secure);
        return locale_url($url_result);
    }
}

if (!function_exists("get_identity")) {
    function get_identity(): array {
        $default = config("cms.identity.default");
        return config("cms.identity.$default", []);
    }
}

//Blade Directive
if (!function_exists('role_input')) {
    function role_input(BaseModel $model, $input): string {
        return $model->isInputAllowed($input) ? "" : "not-allowed";
    }
}

//Blade Directive
if (!function_exists('shorten_text')) {
    function shorten_text($text, $wordsCount = 75) {
        $textParts = explode(' ', strip_tags($text));
        if (count(is_countable($textParts) ? $textParts : []) <= $wordsCount)
            return $text;
        return join(' ', array_slice($textParts, 0, $wordsCount)) . '...';
    }
}

//TODO: must be moved to a private helpers file.
if (!function_exists('get_unshared_content')) {
    function get_unshared_content(string $identifier, WebPage $web_page): string {
        $data = unserialize($web_page->data);
        if ($data != '' and $data != null and isset($data[$identifier]))
            return $data[$identifier]->getContent();
        return '';
    }
}

if (!function_exists('get_gallery')) {
    /**
     * @param string $galleryName
     * @return bool|Gallery
     */
    function get_gallery(string $galleryName) {
        $result = Gallery::where("identifier", $galleryName)->first();
        if ($result != null)
            return $result;
        return false;
    }
}

if (!function_exists('get_gallery_items')) {
    /**
     * Notice : don't use this function in back because use of isMobile()
     * @param string $gallery_name
     * @param int $count
     * @param bool $random_select
     * @return GalleryItem[]
     */
    function get_gallery_items(string $gallery_name, int $count = -1,
                               bool   $random_select = false) {
        $gallery = false;
        if (DetectService::isMobile())
            $gallery = get_gallery($gallery_name . "_mobile");
        if ($gallery === false)
            $gallery = get_gallery($gallery_name);
        if ($gallery !== false) {
            if ($random_select === false) {
                if ($count == -1) {
                    return $gallery->items()->visible()->orderBy('priority', 'ASC')->get();
                }
                return $gallery->items()->visible()->orderBy('priority', 'ASC')->take($count)->get();
            }
            if ($count == -1) {
                return $gallery->items()->visible()->inRandomOrder()->get();
            }
            return $gallery->items()->visible()->inRandomOrder()->take($count)->get();
        }
        return [];
    }
}

if (!function_exists('get_locale')) {
    /**
     * this function returns the current locale of application
     * @return string
     */
    function get_locale(): string {
        return app()->getLocale();
    }
}

if (!function_exists('get_user')) {
    function get_user(string $guard = null): bool|Authenticatable|null {
        if (auth($guard)->check()) {
            if (!auth('web_eloquent')->check()) {
                $user = User::getEloquentObject(auth($guard)->user());
                auth('web_eloquent')->login($user);
            }
            return auth('web_eloquent')->user();
        }
        return false;
    }
}

if (!function_exists('get_customer_user')) {
    function get_customer_user(string $guard = null): bool|CustomerUser {
        if (auth($guard)->check() and get_user($guard)?->is_customer_user)
            return get_user($guard)?->customerUser;
        return false;
    }
}

if (!function_exists('get_customer_legal_info')) {
    function get_customer_legal_info(): bool|CustomerUserLegalInfo {
        $customer = get_customer_user();
        if ($customer)
            return $customer->legalInfo;
        return false;
    }
}

if (!function_exists('customer_need_list_exists')) {
    function customer_need_list_exist(Product $product): bool {
        if (!isset($product->is_in_need_list)) {
            try {
                $product->is_in_need_list = get_customer_user()->needList->contains($product->id);
            } catch (Exception $exception) {
                $product->is_in_need_list = false;
            }

        }
        return $product->is_in_need_list;
    }
}

if (!function_exists('customer_cart_count')) {
    function customer_cart_count(): int {
        $customer = get_customer_user();
        if ($customer !== false) {
            return $customer->cartRows()->count();
        } else {
            return count(get_local_cart());
        }
    }
}

if (!function_exists('pending_invoices_count')) {
    function pending_invoices_count(): bool|int {
        //TODO: I thinks this should be a model scope.
        $customer = get_customer_user();
        if ($customer !== false) {
            return $customer->invoices()->whereIn('payment_status', [
                PaymentStatus::PENDING,
                PaymentStatus::CANCELED,
                PaymentStatus::FAILED
            ])->where('is_active', true)->count();
        }
        return false;
    }
}

if (!function_exists('get_local_cart')) {
    function get_local_cart(bool $full_data = false): array {
        $cart_data = [];
        if (key_exists(env("SITE_LOCAL_CART_COOKIE_NAME"), $_COOKIE)) {
            $cart_data = json_decode($_COOKIE[env("SITE_LOCAL_CART_COOKIE_NAME")], true);
        }

        $result = [];

        $product_ids = array_keys($cart_data);
        $products = Product::whereIn("id", $product_ids)->get();
        foreach ($products as $product) {
            $row_data = $cart_data["{$product->id}"];
            $std_row = new stdClass();
            $std_row->count = $row_data["count"];
            $std_row->product_id = $product->id;

            if ($full_data) {
                $std_row->product = $product;
                $std_row->id = $product->id;
            }

            $result[] = $std_row;
        }

        return array_reverse($result);
    }
}

if (!function_exists('get_system_user')) {

    function get_system_user(string $guard = null): ?SystemUser {
        if (auth($guard)->check() and auth($guard)->user()->is_system_user)
            return SystemUser::where('user_id', auth($guard)->id())->first();
        return null;
    }
}

if (!function_exists('get_system_users')) {

    /**
     * @param string|null $guard
     * @return SystemUser[]|bool
     */
    function get_system_users(string $guard = null) {
        if (auth($guard)->check() and auth($guard)->user()->is_system_user)
            return SystemUser::all();
        return false;
    }
}

if (!function_exists('is_customer')) {
    /**
     * @param string|null $guard
     * @return bool
     */
    function is_customer(string $guard = null): bool {
        return auth($guard)->check() and auth($guard)->user()->is_customer_user;
    }
}

if (!function_exists('app_navbar_directories')) {
    /**
     * @return mixed
     */
    function app_navbar_directories(array $conditions = []) {
        $tree = build_directories_tree(conditions: $conditions);
        return array_filter($tree, function ($root_item) {
            return $root_item->show_in_app_navbar;
        });
    }
}

if (!function_exists('navbar_directories')) {
    function navbar_directories(array $conditions = []): array {
        $tree = build_directories_tree(conditions: $conditions);
        return array_filter($tree, function ($root_item) {
            return $root_item->show_in_navbar;
        });
    }
}

if (!function_exists('footer_directories')) {
    function footer_directories(array $conditions = []): array {
        $tree = build_directories_tree(conditions: $conditions);
        return array_filter($tree, function ($root_item) {
            return $root_item->show_in_footer;
        });
    }
}

if (!function_exists('mobile_footer_directories')) {
    /**
     * @deprecated
     */
    function mobile_footer_directories(array $conditions = []): array {
        return only_footer_directories($conditions);
    }
}

if (!function_exists('only_footer_directories')) {
    function only_footer_directories(array $conditions = []): array {
        $tree = build_directories_tree(conditions: $conditions);
        return array_filter($tree, function ($root_item) {
            return !$root_item->show_in_navbar and $root_item->show_in_footer;
        });
    }
}

if (!function_exists('directory_url')) {
    /**
     * @deprecated
     */
    function directory_url(Directory $directory, bool $forMenu = false): string {
        if ($forMenu and
            $directory->content_type === DirectoryType::PRODUCT and
            $directory->directories()->count() > 0)
            return '#';
        return $directory->getFrontUrl();
    }
}

if (!function_exists('is_directory_group_manual')) {
    function is_directory_group_manual(): bool {
        /** @var CMSSettingHelper $setting_service */
        $setting_service = app(CMSSettingHelper::class);
        return $setting_service->getCMSSettingAsBool('is_directory_group_manual');
    }
}

if (!function_exists('directory_make_children_groups')) {
    function directory_make_children_groups(?Directory $directory, int $column_count): array {
        if ($directory == null)
            return [];

        $tree = $directory->relationLoaded("directories") ? $directory->directories : build_directories_tree($directory);
        $groups = [];

        if (is_directory_group_manual()) {
            foreach ($tree as $sub_directory) {
                if ($sub_directory->show_in_navbar) {
                    if (!isset($groups[$sub_directory->priority % $column_count]))
                        $groups[$sub_directory->priority % $column_count] = [];
                    $groups[$sub_directory->priority % $column_count][] = $sub_directory;
                }
            }
        } else {
            $groups_length = [];

            for ($i = 0; $i < $column_count; $i++) {
                $groups_length[] = 0;
            }

            foreach ($tree as $sub_directory) {
                if (!$sub_directory->show_in_navbar)
                    continue;

                $index = array_search(min($groups_length), $groups_length);
                $groups_length[$index] += 1.9 + count($sub_directory->directories);
                if (!isset($groups[$index]))
                    $groups[$index] = [];
                $groups[$index][] = $sub_directory;
            }
        }
        return $groups;
    }
}

if (!function_exists('get_product_root')) {
    /**
     * @deprecated
     */
    function get_product_root() {
        return Directory::roots()->from(DirectoryType::PRODUCT)->first();
    }
}

if (!function_exists('get_products_root_list_with_type')) {
    /**
     * @deprecated
     */
    function get_products_root_list_with_type($data_type = null) {
        return Directory::roots()->from($data_type)->orderBy('priority', 'ASC')->get();
    }
}

if (!function_exists("get_directory")) {
    function get_directory($directory_id): ?Directory {
        return Directory::find($directory_id);
    }
}

if (!function_exists('get_directory_root')) {
    /**
     * @deprecated
     */
    function get_directory_root($data_type = null) {
        return ($data_type != null and is_numeric($data_type)) ?
            Directory::roots()->from(DirectoryType::REAL)->where("data_type", $data_type)->first()
            : Directory::roots()->from(DirectoryType::REAL)->first();
    }
}

if (!function_exists('get_directory_children_chunk')) {
    /**
     * @deprecated
     */
    function get_directory_children_chunk($directory, $chunk) {
        return $directory != null ?
            $directory->directories()->where("is_internal_link", "is", false)->with('directories')
                ->orderBy('priority')->get()->chunk($chunk) : null;
    }
}

if (!function_exists('get_directory_children')) {
    /**
     * @deprecated
     */
    function get_directory_children($directory, $count = null) {
        return $directory != null ?
            ($count != null) ? $directory->directories()->with('directories')->orderBy('priority')->take($count)->get()
                : $directory->directories()->with('directories')->orderBy('priority')->get() : null;
    }
}

if (!function_exists('get_directory_products')) {
    /**
     * @deprecated
     */
    function get_directory_products($directory, $count = null) {
        return $count != null ?
            $directory->products()->mainModels()->visible()->orderBy('priority')->take($count)->get() :
            $directory->products()->mainModels()->visible()->orderBy('priority')->get();
    }
}

if (!function_exists('get_important_product_leaves')) {
    function get_important_product_leaves(Directory $root_directory, int $count): array|Collection {
        return $root_directory->leafProducts()->mainModels()->visible()
            ->where('important_at', '!=', null)
            ->orderBy('important_at', 'DESC')
            ->orderBy('updated_at', 'DESC')
            ->take($count)->get();
    }
}
if (!function_exists('get_visible_product_leaves')) {
    function get_visible_product_leaves(Directory $root_directory, int $count): array|Collection {
        return $root_directory->leafProducts()->mainModels()->visible()->isActive()
            ->orderBy('important_at', 'DESC')
            ->take($count)->get();
    }
}

if (!function_exists('get_directory_product_leaves')) {
    function get_directory_product_leaves(Directory $root_directory, int $count, $only_active_items = true): array|Collection {
        $result = $root_directory->leafProducts()->mainModels()->visible();
        $tmp_result = clone $result;
        if ($only_active_items or $tmp_result->isActive()->count() >= $count)
            $result = $result->isActive();
        return $result->orderBy("important_at", "desc")->take($count)->get();
    }
}

if (!function_exists('latest_products')) {
    function latest_products(int $count = 8): array|Collection {
        if ($count > 0) {
            return Product::mainModels()->visible()
                ->orderBy('important_at', 'DESC')
                ->orderBy('updated_at', 'DESC')
                ->where("is_active", true)
                ->take($count)->get();
        }
        return [];
    }
}

if (!function_exists('rated_products')) {
    function rated_products(int $count = 8): array|Collection {
        if ($count > 0)
            return Product::mainModels()->visible()->popular()->where("is_active", true)->take($count)->get();
        return [];
    }
}

if (!function_exists('custom_query_products')) {
    function custom_query_products(string $identifier): array|Collection {
        try {
            return ProductQuery::findByIdentifier($identifier)->getProducts();
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('custom_query_product_ids')) {
    function custom_query_product_ids(string $identifier): array|Collection {
        try {
            return ProductQuery::findByIdentifier($identifier)->getProductIds();
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('get_product_filter')) {
    function get_product_filter(string $identifier): ProductFilter {
        try {

            return ProductFilter::findByIdentifier($identifier);
        } catch (Exception $e) {
            return new ProductFilter();
        }
    }
}

if (!function_exists('custom_filter_products')) {
    function custom_filter_products(string $identifier): array|Collection {
        try {
            return ProductFilter::findByIdentifier($identifier)->getProducts();
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('custom_filter_product_ids')) {
    function custom_filter_product_ids(string $identifier): array|Collection {
        try {
            return ProductFilter::findByIdentifier($identifier)->getProductIds();
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('get_filter_data')) {
    function get_filter_data(array $product_ids): array {
        return \App\Services\Product\ProductService::getFilterData($product_ids);
    }
}

if (!function_exists('important_products')) {
    function important_products(int $count = 8): array|Collection {
        if ($count > 0) {
            return Product::important()
                ->orderBy('important_at', 'DESC')
                ->where("is_active", true)
                ->mainModels()
                ->visible()
                ->take($count)->get();
        }
        return [];
    }
}

if (!function_exists('get_customer_addresses')) {
    function get_customer_addresses() {
        return get_customer_user()->addresses;
    }
}

if (!function_exists('get_district')) {
    /**
     * @deprecated
     */
    function get_district(CustomerAddress $address): string {
        return $address->district ? $address->district->name : '';
    }
}

if (!function_exists('get_city')) {
    /**
     * @deprecated
     */
    function get_city(CustomerAddress $address): string {
        return $address->city ? $address->city->name : '';
    }
}

if (!function_exists('get_state')) {
    /**
     * @deprecated
     */
    function get_state(CustomerAddress $address): string {
        return $address->state ? $address->state->name : '';
    }
}

if (!function_exists('get_state_json_by_id')) {
    /**
     * @deprecated
     */
    function get_state_json_by_id($id) {
        $state = State::find($id);
        return $state ? json_encode($state) : json_encode([]);
    }
}

if (!function_exists('get_city_json_by_id')) {
    /**
     * @deprecated
     */
    function get_city_json_by_id($id) {
        $city = City::find($id);
        return $city ? json_encode($city) : json_encode([]);
    }
}

if (!function_exists('get_district_json_by_id')) {
    /**
     * @deprecated
     */
    function get_district_json_by_id($id) {
        $district = District::find($id);
        return $district ? json_encode($district) : json_encode([]);
    }
}

if (!function_exists('get_invoices')) {
    function get_invoices() {
        return get_customer_user()->invoices()->orderBy('id', 'DESC')->paginate(Invoice::getFrontPaginationCount());
    }
}

if (!function_exists('get_blog_categories')) {
    function get_blog_categories($directory) {
        if (count(is_countable($directory->directories) ? $directory->directories : []) > 0)
            return $directory->directories;
        else if ($directory->directory_id != null)
            return $directory->parentDirectory->directories;
        return [$directory];
    }
}

if (!function_exists('get_popular_blog')) {
    function get_popular_blog($count, $type) {
        return Article::popular()->from($type)->with('directory')->take($count)->get();
    }
}

if (!function_exists('get_latest_blog')) {
    function get_latest_blog($type, $count) {
        if (config("wp.enabled")) {
            return WPPost::latest()->take($count)->get();
        }
        return Article::from($type)->latest()->with('directory')->take($count)->get();
    }
}

if (!function_exists('get_suggested_blog')) {
    function get_suggested_blog($type, $count) {
        if (config("wp.enabled")) {
            //TODO: this algorithm should be changed for fetching suggested blog.
            return WPPost::latest()->skip(2)->take($count)->get();
        }
        return Article::suggested()->from($type)->with('directory')->take($count)->get();
    }
}

if (!function_exists('get_system_messages')) {
    function get_system_messages() {
        try {
            $messages = SystemMessageService::getMessages();
            SystemMessageService::flushMessages();
            return $messages;
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('has_system_messages')) {
    function has_system_messages(): bool {
        return SystemMessageService::hasMessages();
    }
}

if (!function_exists('get_months')) {
    function get_months(): array {
        return [
            "فروردین",
            "اردیبهشت",
            "خرداد",
            "تیر",
            "مرداد",
            "شهریور",
            "مهر",
            "آبان",
            "آذر",
            "دی",
            "بهمن",
            "اسفند",
        ];
    }
}

if (!function_exists('get_years')) {
    function get_years(): array {
        $start_year = 1300;
        $end_year = JDateTime::date('Y', time(), false);
        return range($start_year, $end_year);
    }
}

if (!function_exists('hide_number')) {
    function hide_number($number) {
        for ($i = 3; $i < strlen($number) - 3; $i++)
            $number[$i] = '*';
        return $number;
    }
}

if (!function_exists('hide_text')) {
    function hide_text($text): string {
        return substr($text, 0, 4) . "*******" . substr($text, strlen($text) - 4);
    }
}

if (!function_exists('get_payment_drivers')) {
    /**
     * @throws \App\Utils\PaymentManager\Exceptions\PaymentInvalidDriverException
     */
    function get_payment_drivers() {
        return Provider::getEnabledDrivers(true);
    }
}

if (!function_exists('is_default_payment_driver')) {
    function is_default_payment_driver($driver): bool {
        return Provider::isDefaultDriver($driver);
    }
}

if (!function_exists('get_disabled_setting_appliances')) {
    function get_disabled_setting_appliances(): array {
        $disabled_setting_appliances = env('DISABLED_APPLIANCES', '');
        if (strlen($disabled_setting_appliances) == 0)
            return [];
        return explode(',', $disabled_setting_appliances);
    }
}

if (!function_exists('is_selected')) {
    /**
     * @deprecated
     */
    function is_selected(Directory $directory): bool {
        return Request::segment(1) == $directory->url_part;
    }
}

if (!function_exists('get_configurations')) {
    function get_configurations($needsJson = false, $prefix = "") {
        $prefixLength = strlen($prefix);
        $envFileAddress = public_path() . "/../.env";
        $envFile = fopen($envFileAddress, "r");
        $envFileContent = fread($envFile, filesize($envFileAddress));
        fclose($envFile);

        $envFileLines = explode("\n", $envFileContent);
        $result = [];

        foreach ($envFileLines as $line) {
            if (strlen($line) > 0 and ($prefixLength === 0 or
                    substr($line, 0, $prefixLength) === $prefix)) {
                $parts = explode("=", $line);
                if (count(is_countable($parts) ? $parts : []) == 2) {
                    /**
                     * TODO: this way is not efficient
                     * the reason I used env function is to have real type of values, for example $parts[1] is "true"
                     * but I needed the variable to be boolean type true.
                     */
                    $result[$parts[0]] = env($parts[0]);
                }
            }
        }
        return $needsJson ? json_encode($result) : $result;
    }
}

if (!function_exists('get_searched_products')) {
    function get_searched_products() {
        return Product::search(request('query'))->mainModels()->visible()->get();
    }
}

if (!function_exists('get_digits')) {
    function get_digits($lang) {
        $digits = [
            "fa" => explode(",", "۱,۲,۳,۴,۵,۶,۷,۸,۹,۰"),
            "en" => explode(",", "1,2,3,4,5,6,7,8,9,0")
        ];
        if ($lang != null and key_exists($lang, $digits))
            return $digits[$lang];
        return [];
    }
}

if (!function_exists('convert_digits')) {
    /**
     * @param int|string $number
     * @param string $from
     * @param string $to
     * @return string
     */
    function convert_digits($number, string $from = "en", string $to = "fa"): string {
        $fromList = get_digits($from);
        $toList = get_digits($to);
        $number = "{$number}";
        foreach ($fromList as $index => $fromDigit) {
            $number = str_replace($fromDigit, $toList[$index], $number);
        }
        return $number;
    }
}

if (!function_exists('format_price')) {
    /**
     * @param integer|string $price
     * @param string $lang
     * @return string
     */
    function format_price($price, string $lang = "fa"): string {
        $price = intval($price);
        $separator = $lang == "fa" ? "،" : ",";
        $price = number_format($price, 0, '.', $separator);
        return $lang == "fa" ?
            convert_digits($price, "en", "fa") :
            $price;
    }
}

if (!function_exists('is_paste_possible')) {
    function is_paste_possible($directory): bool {
        try {
            return ClipBoardService::isPastePossible($directory);
        } catch (EmptyClipBoardException|InvalidTypeException $e) {
            return false;
        }
    }
}

if (!function_exists('get_product_color_models')) {
    function get_product_color_models(Product $product) {
        return Product::models($product, false)
            ->orderBy('id', 'DESC')->groupBy('color_code')->get();
    }
}

if (!function_exists('get_product_last_color')) {
    function get_product_last_color(Product $product): Color {
        return $product->colors()->orderBy('id', 'DESC')->first();
    }
}

if (!function_exists('get_product_accessories')) {
    function get_product_accessories(Product $product) {
        return $product->accessories()->mainModels()->visible()->get();
    }
}

if (!function_exists('get_product_related_articles')) {
    function get_product_related_articles(Product $product, $type, int $count = 3) {
        $tags = $product->tags()->get()->pluck("id");
        return Article::from($type)->whereHas("tags", function ($query) use ($tags) {
            $query->whereIn("id", $tags);
        })->latest()->take($count)->get();
    }
}
if (!function_exists('get_product_related_products')) {
    /**
     * @param Product $product
     * @param int $count
     * @return mixed
     */
    function get_product_related_products(Product $product, int $count = 5) {
        $directory = $product->directory;
        $leafProducts = new Collection();
        $products = $directory->products()->mainModels()->visible()
            ->where("is_active", true)->latest()->except($product->id)->take($count)->get();
        while ($products->count() < $count and $directory->directory_id != null
            and $leafProducts->count() < $count) {
            $directory = $directory->parentDirectory;
            $leafProducts = $directory->leafProducts()->mainModels()->visible()
                ->where("is_active", true)->latest()->except($product->id)->take($count)->get();
        }
        return $products->merge($leafProducts)->unique()->take($count);
    }
}

if (!function_exists('get_product_similar_products')) {
    /**
     * @param Product $product
     * @param int $count
     * @param int $key_id
     * @return Product[]
     */
    function get_product_similar_products(Product $product, int $count = 5, int $key_id = 0) {
        if ($key_id === 0) {
            $key_id = $product->attributeKeys()->where("is_sortable", true)
                ->pluck("p_structure_attr_keys.id")->get(0);
        }

        $product_key_values = $product->pAttributes()
            ->where('p_structure_attr_key_id', $key_id)
            ->pluck('p_structure_attr_value_id')->toArray();

        $product_other_keys_attrs = $product->pAttributes()
            ->where('p_structure_attr_key_id', '!=', $key_id)->get();
        $product_other_keys_values = [];
        foreach ($product_other_keys_attrs as $product_key_value) {
            $item['key'] = $product_key_value->p_structure_attr_key_id;
            $item['value'] = $product_key_value->p_structure_attr_value_id;
            array_push($product_other_keys_values, $item);
        }

        $products = [];
        if ($key_id !== 0) {
            $p_structure = $product->productStructure;
            if ($p_structure != null) {
                $products = $p_structure->products()->where('directory_id',
                    $product->directory_id)->mainModels()->visible()->except($product->id)
                    ->whereHas('pAttributes', function ($q1) use ($key_id, $product_key_values) {
                        $q1->where('p_structure_attr_key_id', $key_id)
                            ->whereNotIn('p_structure_attr_value_id', $product_key_values);
                    })->where(function ($q2) use ($key_id, $product_other_keys_values) {
                        foreach ($product_other_keys_values as $item) {
                            $key = $item['key'];
                            $value = $item['value'];
                            $q2->whereHas('pAttributes', function ($q3) use ($key_id, $key, $value) {
                                $q3->where('p_structure_attr_key_id', '!=', $key_id);
                                $q3->where([
                                    'p_structure_attr_key_id' => $key,
                                    'p_structure_attr_value_id' => $value
                                ]);
                            });
                        }
                    })->orderBy("priority", "ASC")->take($count)->get();
            }
        }
        return $products;
    }
}

if (!function_exists('get_related_products_with_directory_level')) {
    function get_related_products_with_directory_level(Product $product, int $count = 5, int $level = 1) {
        $directory = $product->directory;
        if ($level > 1) {
            while ($level != 1) {
                $directory = $directory->parentDirectory;
                $level--;
            }
        }
        return $directory->leafProducts()->mainModels()->visible()->except($product->id)
            ->orderBy("priority", "ASC")->take($count)->get();
    }
}
if (!function_exists('get_product_attributes')) {
    function get_product_attributes(Product $product = null) {
        if ($product != null) {
            $attributes = PAttr::getProductAttributes($product);
            return $attributes['attributes'];
        } else {
            return null;
        }
    }
}

if (!function_exists('get_product_most_privileged_key_attributes')) {
    /**
     * @deprecated
     */
    function get_product_most_privileged_key_attributes(int $count = 9): array {
        $id = PStructureAttrKey::orderBy('priority', 'DESC')->pluck('id')->first();
        $attributeValues = PStructureAttrValue::with('key')->where('p_structure_attr_key_id', $id)
            ->inRandomOrder()
            ->paginate($count);
        return $attributeValues->items();
    }
}

if (!function_exists('get_product_by_id')) {
    function get_product_by_id($id): ?Product {
        return ($id != null) ? Product::find($id) : null;
    }
}

if (!function_exists('get_article_related_products')) {
    function get_article_related_products(Article $article, int $count = 3) {
        $tags = $article->tags()->get()->pluck('id');
        return Product::mainModels()->visible()->whereHas('tags', function ($query) use ($tags) {
            $query->whereIn('id', $tags);
        })->where("is_active", true)->mainModels()->visible()->latest()->take($count)->get();
    }
}

if (!function_exists('get_article_related_articles')) {
    function get_article_related_articles(Article $article, int $count = 4) {
        return $article->directory->articles()->latest()->except($article->id)->take($count)->get();
    }
}

if (!function_exists('get_experts')) {
    /**
     * @deprecated
     */
    function get_experts(int $count = 4) {
        return SystemUser::where('is_expert', true)->take($count)->get();
    }
}

if (!function_exists('recaptcha_enabled')) {
    function recaptcha_enabled(): bool {
        return !str_contains(env("TEMPORARILY_DISABLED_RULES", ""), "g-recaptcha-response");
    }
}

if (!function_exists('get_same_models_products')) {
    /**
     * @param $product
     * @return array
     */
    function get_same_models_products($product): array {
        $products = Product::models($product, false)
            ->with('productStructure', 'images', 'rates')
            ->get();
        return json_decode($products);
    }
}


if (!function_exists('check_cart')) {
    function check_cart($product_id): bool {
        $customer = get_customer_user();
        if ($customer !== false) {
            return $customer->cartRows()->where("product_id", $product_id)->count() > 0;
        } else {
            $cart_rows = get_local_cart();
            foreach ($cart_rows as $cart_row) {
                if ($cart_row->product_id === $product_id) {
                    return true;
                }
            }
            return false;
        }
    }
}

if (!function_exists('get_cart_information')) {
    function get_cart_information($product_id) {
        $customer = get_customer_user();
        $selected_row = null;
        if ($customer !== false) {
            $selected_row = $customer->cartRows()->with('product')->where("product_id", $product_id)->first();
        } else {
            $cart_rows = get_local_cart();
            foreach ($cart_rows as $cart_row) {
                if ($cart_row->product_id === $product_id) {
                    $product = Product::find($product_id);
                    $selected_row = $cart_row;
                    $selected_row->product = $product;
                    $selected_row->id = $product->id;
                }
            }
        }
        return $selected_row;
    }
}

if (!function_exists('get_cart')) {
    function get_cart(): \Illuminate\Database\Eloquent\Collection|array {
        $customer = get_customer_user();
        if ($customer !== false) {
            $cart_rows = $customer->cartRows()->with('product')->orderBy('id', 'DESC')->get();
        } else {
            $cart_rows = get_local_cart(true);
        }
        return $cart_rows;
    }
}

if (!function_exists('get_breadcrumb')) {
    /**
     * @deprecated
     */
    function get_breadcrumb(Directory $directory): string {
        if (!(isset($directory->parentDirectory)))
            return '<li class="active">' . $directory->title . '</li>';
        else {
            return "<li><a href=" . $directory->url_full . ">" . $directory->title . "</a></li>"
                . get_breadcrumb($directory->parentDirectory);
        }
    }
}

if (!function_exists('get_minimum_purchase_free_shipment')) {
    function get_minimum_purchase_free_shipment() {
        try {
            return ShipmentCostService::getRecord()->getMinimumPurchaseFreeShipment();
        } catch (Exception $e) {
            Log::error('Message : ' . $e->getmessage());
            return 'not set';
        }
    }
}
if (!function_exists('product_disable_on_min')) {
    function product_disable_on_min(): ?string {
        /** @var CMSSettingHelper $setting_service */
        $setting_service = app(CMSSettingHelper::class);
        return $setting_service->getCMSSettingAsBool(CMSSettingKey::DISABLE_PRODUCT_ON_MIN);
    }
}

if (!function_exists('get_state_deactivate_product')) {
    /**
     * @deprecated
     */
    function get_state_deactivate_product($product) {
        return $product->inaccessibility_type;
    }
}

if (!function_exists('get_inquiry_call_number')) {
    /**
     * @deprecated
     */
    function get_inquiry_call_number(): string {
        /** @var \App\Helpers\CMSSettingHelper $setting_service */
        $setting_service = app(CMSSettingHelper::class);
        return $setting_service->getCMSSettingAsString(CMSSettingKey::INQUIRY_CALL_NUMBER);
    }
}

if (!function_exists('customer_can_edit_profile')) {
    function customer_can_edit_profile(): bool {
        /** @var \App\Helpers\CMSSettingHelper $setting_service */
        $setting_service = app(CMSSettingHelper::class);
        return $setting_service->getCMSSettingAsBool(CMSSettingKey::CUSTOMER_CAN_EDIT_PROFILE);
    }
}

if (!function_exists('get_root_directory_per_directory')) {
    function get_root_directory_per_directory(Directory $directory) {
        foreach ($directory->getParentDirectories() as $dir) {
            if ($dir->directory_id == null)
                return $dir;
        }
        return $directory;
    }
}

if (!function_exists('h_view')) {
    /**
     * @param null $template
     * @param array $data
     * @return Factory|Application|View
     */
    function h_view($template = null, array $data = []) {
        if (DetectService::isMobile() and view()->exists($template . "_mobile"))
            $template = $template . "_mobile";
        elseif (request()->has("app") and request("app") and view()->exists($template . "_app"))
            $template = $template . "_app";
        if (\App\Utils\CMS\Setting\Language\LanguageSettingService::isMultiLangSystem()) {
            $locale_template = $template . "___locale_" . app()->getLocale();
            if (\Illuminate\Support\Facades\View::exists($locale_template))
                $template = $locale_template;
        }
        return view($template, $data);
    }
}

if (!function_exists("is_multi_lang")) {
    function is_multi_lang(): bool {
        return \App\Utils\CMS\Setting\Language\LanguageSettingService::isMultiLangSystem();
    }
}

if (!function_exists("is_rtl")) {
    function is_rtl() {
        return \App\Utils\CMS\Setting\Language\LanguageSettingService::isRTLSystem();
    }
}

if (!function_exists('get_cms_setting')) {
    function get_cms_setting(string $key): string {
        /** @var CMSSettingHelper $setting_service */
        $setting_service = app(CMSSettingHelper::class);
        return $setting_service->getCMSSettingAsString($key);
    }
}

if (!function_exists('get_template_views')) {
    function get_template_views(): array {
        return array_map(function ($blade_name) {
            return str_replace(".blade.php", "", $blade_name);
        }, \App\Utils\CMS\Template\TemplateService::getOriginalBlades(true));
    }
}

if (!function_exists("get_current_customer_location_title")) {
    function get_current_customer_location_title(): string {
        $customer_location = \App\Utils\CMS\Setting\CustomerLocation\CustomerLocationService::getRecord();
        if ($customer_location != null)
            return "{$customer_location->getState()->name}، {$customer_location->getCity()->name}";
        return "لطفا شهر و استان خود را مشخص کنید";
    }
}

if (!function_exists("get_current_customer_location_data")) {
    function get_current_customer_location_data(): ?array {
        $customer_location = \App\Utils\CMS\Setting\CustomerLocation\CustomerLocationService::getRecord("");
        if ($customer_location != null)
            return [
                "state_id" => $customer_location->getState()->id,
                "city_id" => $customer_location->getCity()->id
            ];
        return null;
    }
}

if (!function_exists("get_customer_meta_categories")) {
    function get_customer_meta_categories(): \Illuminate\Database\Eloquent\Collection|array {
        return CustomerMetaCategory::main()->get();
    }
}

if (!function_exists("cmc_get_options")) {
    function cmc_get_options($identifier, $customer_meta_category): array {
        try {
            return explode(";", cmc_get_content($identifier, $customer_meta_category));
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists("cmc_get_content")) {
    function cmc_get_content($identifier, $customer_meta_category): string {
        try {
            return (Arr::first($customer_meta_category->data_object, function ($iter_item) use ($identifier) {
                return $iter_item->input_identifier === $identifier;
            }))->input_content;
        } catch (Exception $e) {
            return "";
        }
    }
}

if (!function_exists("get_shipment_cost")) {
    function get_shipment_cost(Invoice $invoice, $state_id = 0): int {
        if ($state_id !== 0)
            $invoice->state_id = $state_id;
        return $invoice->calculateShipmentCost();
    }
}

if (!function_exists("build_directories_tree")) {
    function build_directories_tree(?Directory $root = null, array $conditions = [], array $order = []): array {
        $directories = Directory::permitted()->where($conditions)
            ->orderBy($order["column"] ?? "priority", $order["direction"] ?? "ASC")->get();
        $branch = [];
        $parts = [];
        $map = [];

        foreach ($directories as $directory) {
            $map[$directory->id] = $directory;
            $directory->setRelation("directories", []);
            if (!isset($parts[$directory->directory_id]))
                $parts[$directory->directory_id] = [];
            $parts[$directory->directory_id][] = $directory;
        }

        foreach ($parts as $parent_id => $children) {
            if (isset($map[$parent_id]))
                $map[$parent_id]->setRelation("directories", $children);
            else {
                $branch = array_merge($branch, $children);
            }
        }

        return $root == null ? $branch : ($map[$root->id]->directories ?? []);
    }
}

if (!function_exists("clean_cart_cookie")) {
    function clean_cart_cookie() {
        CartProvider::cleanCookie();
    }
}

if (!function_exists("get_structure_sort_title")) {
    /**
     * @param PStructureAttrKey[] $keys
     */
    function get_structure_sort_title($keys) {
        $sort_data_title = false;
        foreach ($keys as $key) {
            if ($key->is_sortable) {
                if ($sort_data_title === false) {
                    $sort_data_title = $key->title;
                } else {
                    return false;
                }
            }
        }
        return $sort_data_title;
    }
}

if (!function_exists("get_logistics_schedule")) {
    function get_logistics_schedule(bool $contains_disabled = true) {
        $data = LogisticService::getPublicTableCells();
        if (!$contains_disabled) {
            foreach ($data as $day_index => $day) {
                $is_enabled = false;
                foreach ($day as $period_index => $period) {
                    $is_enabled = ($is_enabled or $period["is_enabled"]);
                }
                if (!$is_enabled)
                    unset($data[$day_index]);
            }
        }
        return $data;
    }
}

if (!function_exists("day_of_week")) {
    function day_of_week(int $diff) {
        return \App\Utils\Jalali\JDate::forge(\Illuminate\Support\Carbon::now()->addDay($diff))->format("%A");
    }
}

if (!function_exists("get_current_formal_date")) {
    function get_current_formal_date() {
        return \App\Utils\Common\TimeService::getCurrentFormalDate();
    }
}

if (!function_exists("get_current_date")) {
    function get_current_date() {
        return \App\Utils\Common\TimeService::getCurrentDate();
    }
}

if (!function_exists("get_max_transaction_amount")) {
    function get_max_transaction_amount(): int {
        return \App\Utils\PaymentManager\ConfigProvider::getMaxTransactionAmount();
    }
}

if (!function_exists("representative_get_options")) {
    function representative_get_options(): array {
        return \App\Utils\CMS\Setting\Representative\RepresentativeSettingService::getOptions();
    }
}

if (!function_exists("representative_is_enabled")) {
    function representative_is_enabled(): bool {
        return \App\Utils\CMS\Setting\Representative\RepresentativeSettingService::isEnabled();
    }
}

if (!function_exists("representative_is_forced")) {
    function representative_is_forced(): bool {
        return \App\Utils\CMS\Setting\Representative\RepresentativeSettingService::isForced();
    }
}

if (!function_exists("representative_is_customer_representative_enabled")) {
    function representative_is_customer_representative_enabled(): bool {
        return \App\Utils\CMS\Setting\Representative\RepresentativeSettingService::isCustomerRepresentativeEnabled();
    }
}

if (!function_exists("get_product_model_options_multi_level")) {
    function get_product_model_options_multi_level(Product $product): array {
        return \App\Services\Product\ProductModelService::getProductModelOptionsMultiLevel($product);
    }
}

if (!function_exists('get_image_ratio')) {

    /**
     * @param string $category
     * @return string
     */
    function get_image_ratio($category) {
        return config("cms.images.{$category}.ratio");
    }
}

if (!function_exists('get_image_min_width')) {

    /**
     * @param string $category
     * @return integer
     */
    function get_image_min_width($category) {
        return config("cms.images.{$category}.original.width");
    }
}

if (!function_exists('get_image_min_height')) {

    /**
     * @param string $category
     * @return integer
     */
    function get_image_min_height($category) {
        return config("cms.images.{$category}.original.height");
    }
}

if (!function_exists('get_video_max_size')) {

    /**
     * @param string $category
     * @return integer
     */
    function get_file_max_size($category) {
        return config("cms.files.{$category}.original.size");
    }
}

if (!function_exists('get_article_type')) {
    function get_article_type($type) {
        return config('cms.template.article-types.' . $type);
    }
}

if (!function_exists('get_product_type')) {
    function get_product_type($type) {
        return config('cms.template.product-types.' . $type);
    }
}

if (!function_exists('get_article_types')) {
    function get_article_types() {
        return config('cms.template.article-types');
    }
}

if (!function_exists('get_product_types')) {
    function get_product_types() {
        return config('cms.template.product-types');
    }
}

if (!function_exists('get_product_inaccessibility_types')) {
    function get_product_inaccessibility_types() {
        return config('cms.template.product-inaccessibility-types');
    }
}

if (!function_exists('get_article_list_blade')) {
    /**
     * @param \App\Models\Directory $directory
     * @return mixed
     */
    function get_article_list_blade($directory) {
        $content_type = get_article_type($directory->data_type)['title'];
        if ($directory->directory_id == null) {
            $category_blade = "public.{$content_type}-categories";
            if ($category_blade != null)
                return $category_blade;
        }
        return "public.{$content_type}-list";
    }
}

if (!function_exists('get_article_single_blade')) {
    function get_article_single_blade($type) {
        $content_type = get_article_type($type)['title'];
        return "public.{$content_type}-single";
    }
}

if (!function_exists('url_encode')) {
    function url_encode($string) {
        return str_replace(" ", "-", trim($string));
    }
}

if (!function_exists('email_encode')) {
    function email_encode($email) {
        $email = strtolower($email);
        $email = str_replace("@", "AT", $email);
        $email = str_replace(".", "DOT", $email);
        return $email;
    }
}


if (!function_exists('email_decode')) {
    function email_decode($email) {
        $email = str_replace("AT", "@", $email);
        $email = str_replace("DOT", ".", $email);
        return $email;
    }
}

if (!function_exists('getUnreadMessage')) {
    function getUnreadMessage() {
        return App\Models\WebFormMessage::where("user_id", null);
    }
}

if (!function_exists("drop_non_ascii")) {
    function drop_non_ascii(string $input = null): ?string {
        return $input !== null ? preg_replace('/[[:^print:]]/', '', $input) : null;
    }
}

if (!function_exists("drop_non_digits")) {
    function drop_non_digits(string $input = null): ?string {
        return $input !== null ? preg_replace("/[^0-9]/", '', $input) : null;
    }
}

if (!function_exists("get_controller_entity_name")) {
    function get_controller_entity_name($controller_name): string {
        $short_class_name = last(explode("\\", $controller_name));
        return Str::snake(str_replace("Controller", "", $short_class_name));
    }
}

if (!function_exists("get_controller_entity_names")) {
    function get_controller_entity_names($namespace = "\\App\\Http\\Controllers\\Admin"): array {
        $result = [];
        foreach ((new ReflectiveNamespace($namespace))->getclassNames() as $class_name) {
            $result[$class_name] = get_controller_entity_name($class_name);
        }
        return $result;
    }
}

if (!function_exists("get_models_entity_names")) {
    function get_models_entity_names($namespace = "\\App\\Models"): array {
        $result = [];
        foreach ((new ReflectiveNamespace($namespace))->getclassNames() as $class_name) {
            $result[$class_name] = get_model_entity_name($class_name);
        }
        return $result;
    }
}

if (!function_exists("get_model_entity_name")) {
    function get_model_entity_name($model_name): string {
        $model_name = last(explode("\\", $model_name));
        return Str::snake($model_name);
    }
}

if (!function_exists("get_model_entity_names")) {
    function get_model_entity_names($namespace = "\\App\\Models"): array {
        $result = [];
        foreach ((new ReflectiveNamespace($namespace))->getclassNames() as $class_name) {
            $result[$class_name] = get_model_entity_name($class_name);
        }
        return $result;
    }
}

if (!function_exists("str_to_dashed")) {
    function str_to_dashed($input): string {
        return strtolower(
            str_replace("_", "-",
                preg_replace('/(?<!^)[A-Z]/', '-$0', $input)
            )
        );
    }
}

if (!function_exists("history_back")) {
    function history_back(): ?string {
        return \App\Utils\Common\History::back();
    }
}

if (!function_exists("modal_html")) {
    function modal_html(): string {
        $path = \Illuminate\Support\Facades\Request::path();
        $modal_route = \App\Models\ModalRoute::findRoute($path);
        if ($modal_route != null) {
            $modal = $modal_route->modal;
            $session_key = 'modal-view-count-' . $modal->id;
            $views_count = 1;
            if (\Illuminate\Support\Facades\Session::has($session_key)) {
                $views_count = \Illuminate\Support\Facades\Session::get($session_key);
            } else {
                \Illuminate\Support\Facades\Session::put($session_key, $views_count);
            }

            if ($views_count < $modal->repeat_count) {
                \Illuminate\Support\Facades\Session::put($session_key, $views_count + 1);
                return $modal->html();
            }
        }

        return '';
    }
}

if (!function_exists('is_manual_stock')) {
    function is_manual_stock(): bool {
        try {
            $financial_driver = \App\Utils\FinancialManager\Provider::getEnabledDriver();
            $financial_driver_config = ConfigProvider::getConfig($financial_driver);
            return $financial_driver_config->is_manual_stock;
        } catch (Exception $exception) {
            return true;
        }
    }
}


if (!function_exists('get_os')) {
    function get_os() {
        if (DetectService::isAndroid())
            return OSType::Android;
        else if (DetectService::isIOS())
            return OSType::IOS;
        return OSType::Other;
    }
}

if (!function_exists('is_desktop')) {
    function is_desktop() {

        return DetectService::isDesktop();
    }
}

if (!function_exists('is_mobile')) {
    function is_mobile() {
        return DetectService::isMobile();
    }
}

if (!function_exists('is_tablet')) {
    function is_tablet() {
        return DetectService::isTablet();
    }
}


if (!function_exists('is_ios')) {
    function is_ios() {
        return DetectService::isIOS();
    }
}


if (!function_exists('is_android')) {
    function is_android() {
        return DetectService::isAndroid();
    }
}