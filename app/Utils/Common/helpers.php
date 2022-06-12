<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/18/17
 * Time: 1:21 PM
 */

use App\Utils\FinancialManager\ConfigProvider;
use App\Utils\Reflection\ReflectiveNamespace;
use Illuminate\Support\Str;

if (!function_exists('get_image_ratio')) {

    /**
     * @param string $category
     * @return string
     */
    function get_image_ratio($category)
    {
        return config("cms.images.{$category}.ratio");
    }
}

if (!function_exists('get_image_min_width')) {

    /**
     * @param string $category
     * @return integer
     */
    function get_image_min_width($category)
    {
        return config("cms.images.{$category}.original.width");
    }
}

if (!function_exists('get_image_min_height')) {

    /**
     * @param string $category
     * @return integer
     */
    function get_image_min_height($category)
    {
        return config("cms.images.{$category}.original.height");
    }
}

if (!function_exists('get_video_max_size')) {

    /**
     * @param string $category
     * @return integer
     */
    function get_file_max_size($category)
    {
        return config("cms.files.{$category}.original.size");
    }
}

if (!function_exists('get_article_type')) {
    function get_article_type($type)
    {
        return config('cms.template.article-types.' . $type);
    }
}

if (!function_exists('get_product_type')) {
    function get_product_type($type)
    {
        return config('cms.template.product-types.' . $type);
    }
}

if (!function_exists('get_article_types')) {
    function get_article_types()
    {
        return config('cms.template.article-types');
    }
}

if (!function_exists('get_product_types')) {
    function get_product_types()
    {
        return config('cms.template.product-types');
    }
}

if (!function_exists('get_product_inaccessibility_types')) {
    function get_product_inaccessibility_types()
    {
        return config('cms.template.product-inaccessibility-types');
    }
}

if (!function_exists('get_article_list_blade')) {
    /**
     * @param \App\Models\Directory $directory
     * @return mixed
     */
    function get_article_list_blade($directory)
    {
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
    function get_article_single_blade($type)
    {
        $content_type = get_article_type($type)['title'];
        return "public.{$content_type}-single";
    }
}

if (!function_exists('url_encode')) {
    function url_encode($string)
    {
        return str_replace(" ", "-", trim($string));
    }
}

if (!function_exists('email_encode')) {
    function email_encode($email)
    {
        $email = strtolower($email);
        $email = str_replace("@", "AT", $email);
        $email = str_replace(".", "DOT", $email);
        return $email;
    }
}


if (!function_exists('email_decode')) {
    function email_decode($email)
    {
        $email = str_replace("AT", "@", $email);
        $email = str_replace("DOT", ".", $email);
        return $email;
    }
}

if (!function_exists('getUnreadMessage')) {
    function getUnreadMessage()
    {
        return App\Models\WebFormMessage::where("user_id", null);
    }
}

if (!function_exists("drop_non_ascii")) {
    function drop_non_ascii(string $input = null): ?string
    {
        return $input !== null ? preg_replace('/[[:^print:]]/', '', $input) : null;
    }
}

if (!function_exists("drop_non_digits")) {
    function drop_non_digits(string $input = null): ?string
    {
        return $input !== null ? preg_replace("/[^0-9]/", '', $input) : null;
    }
}

if (!function_exists("get_controller_entity_name")) {
    function get_controller_entity_name($controller_name): string
    {
        $short_class_name = last(explode("\\", $controller_name));
        return Str::snake(str_replace("Controller", "", $short_class_name));
    }
}

if (!function_exists("get_controller_entity_names")) {
    function get_controller_entity_names($namespace = "\\App\\Http\\Controllers\\Admin"): array
    {
        $result = [];
        foreach ((new ReflectiveNamespace($namespace))->getclassNames() as $class_name) {
            $result[$class_name] = get_controller_entity_name($class_name);
        }
        return $result;
    }
}

if (!function_exists("get_models_entity_names")) {
    function get_models_entity_names($namespace = "\\App\\Models"): array
    {
        $result = [];
        foreach ((new ReflectiveNamespace($namespace))->getclassNames() as $class_name) {
            $result[$class_name] = get_model_entity_name($class_name);
        }
        return $result;
    }
}

if (!function_exists("get_model_entity_name")) {
    function get_model_entity_name($model_name): string
    {
        $model_name = last(explode("\\", $model_name));
        return Str::snake($model_name);
    }
}

if (!function_exists("get_model_entity_names")) {
    function get_model_entity_names($namespace = "\\App\\Models"): array
    {
        $result = [];
        foreach ((new ReflectiveNamespace($namespace))->getclassNames() as $class_name) {
            $result[$class_name] = get_model_entity_name($class_name);
        }
        return $result;
    }
}

if (!function_exists("str_to_dashed")) {
    function str_to_dashed($input): string
    {
        return strtolower(
            str_replace("_", "-",
                preg_replace('/(?<!^)[A-Z]/', '-$0', $input)
            )
        );
    }
}

if (!function_exists("history_back")) {
    function history_back(): ?string
    {
        return \App\Utils\Common\History::back();
    }
}

if (!function_exists("modal_html")) {
    function modal_html(): string
    {
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
    function is_manual_stock(): bool
    {
        try {
            $financial_driver = \App\Utils\FinancialManager\Provider::getEnabledDriver();
            $financial_driver_config = ConfigProvider::getConfig($financial_driver);
            return $financial_driver_config->is_manual_stock;
        } catch (Exception $exception) {
            return true;
        }
    }
}


