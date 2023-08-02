<?php

namespace App\Utils\CMS\Setting\Language;

use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\BaseCMSConfigManager;
use App\Utils\Reflection\AnnotationBadKeyException;
use App\Utils\Reflection\AnnotationBadScopeException;
use App\Utils\Reflection\AnnotationNotFoundException;
use App\Utils\Reflection\AnnotationSyntaxException;
use Illuminate\Support\Str;
use ReflectionException;

/**
 *
 * @method static LanguageSettingModel getRecord($name)
 */
class LanguageSettingService extends BaseCMSConfigManager
{
    protected static string $KEY_POSTFIX = '_language_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;
    private static array $CACHED_DATA = [];

    public static function defaultRecord($name): LanguageSettingModel
    {
        if ($name == config("translation.fallback_locale")) {
            return new LanguageSettingModel($name, true, true);
        }
        return new LanguageSettingModel($name, false, false);
    }

    /**
     * @throws NotValidSettingRecordException
     */
    public static function updateRecord(string $lang_id, bool $is_enabled, bool $is_default): LanguageSettingModel
    {
        $language_model = new LanguageSettingModel($lang_id, $is_enabled, $is_default);
        LanguageSettingService::setRecord($language_model);
        return $language_model;
    }

    public static function getConfig(string $lang_id): LanguageItemModel
    {
        if (!isset(static::$CACHED_DATA[$lang_id])) {
            $language_setting_record = static::getRecord($lang_id);
            self::$CACHED_DATA[$lang_id] = $language_setting_record->getConfig();
        }
        return self::$CACHED_DATA[$lang_id];
    }

    public static function getAll(): array
    {
        $result = [];
        $languages = config("translation.available_locales");
        foreach ($languages as $lang_id) {
            $lang_config = self::getConfig($lang_id);
            $result[$lang_id] = $lang_config;
        }
        return $result;
    }

    /**
     * @param LanguageItemModel[] $languages
     * @throws NotValidSettingRecordException
     */
    public static function setAll(array $languages): void
    {
        $enabled_locales = [];
        $default_locale = "";
        foreach ($languages as $lang_id => $lang_config_data) {
            if (in_array($lang_id, config("translation.available_locales"))) {
                self::$CACHED_DATA[$lang_id] = LanguageSettingService::updateRecord(
                    $lang_id, (bool)$lang_config_data["is_enabled"], (bool)$lang_config_data["is_default"]);

                if ($lang_config_data["is_enabled"]) {
                    $enabled_locales[] = $lang_id;
                }

                if ($lang_config_data["is_default"]) {
                    $default_locale = $lang_id;
                }
            }
        }
        static::setEnabledLocalesToEnvFile($enabled_locales);
        static::setDefaultLocaleToEnvFile($default_locale);
    }


    /**
     * @throws AnnotationNotFoundException
     * @throws AnnotationSyntaxException
     * @throws AnnotationBadScopeException
     * @throws AnnotationBadKeyException
     * @throws ReflectionException
     */
    public static function getRules($languages): array
    {
        $rules = [];
        foreach ($languages as $inputs) {
            $lang_config = new LanguageItemModel();
            foreach ($inputs as $input_key => $value)
                $rules[$input_key] = $lang_config->getInputRule($input_key);
        }
        return array_filter($rules);
    }

    public static function getEnabledLocalesFromEnvFile(): array
    {
        $key = "SITE_ENABLED_LOCALES";
        $value = static::getEnvConfig($key);
        if ($value != null)
            return array_map(function ($item) {
                return trim($item);
            }, explode(',', $value));
        return ["fa"];
    }

    public static function getDefaultLocale(): string
    {
        $key = "SITE_DEFAULT_LOCALE";
        $value = static::getEnvConfig($key);
        return $value ?? "fa";
    }

    public static function setEnabledLocalesToEnvFile(array $languages): void
    {
        static::setEnvConfig("SITE_ENABLED_LOCALES", implode(",", array_map(function ($language) {
            return trim($language);
        }, $languages)));
    }

    public static function setDefaultLocaleToEnvFile(string $language): void
    {
        $key = "SITE_DEFAULT_LOCALE";
        static::setEnvConfig($key, $language);
    }

    private static function setEnvConfig(string $key, string $value): void
    {
        $path = base_path(".env");
        $env_file_contents = file_get_contents($path);
        if (Str::contains($env_file_contents, $key) === false) {
            file_put_contents($path, PHP_EOL . "$key=$value" . PHP_EOL, FILE_APPEND);
        } else {
            file_put_contents($path, implode(PHP_EOL, array_map(function ($line) use ($key, $value) {
                if (Str::startsWith($line, $key))
                    return "$key=$value";
                else
                    return $line;
            }, explode(PHP_EOL, $env_file_contents))));
        }
    }

    private static function getEnvConfig(string $key): ?string
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $lines = explode(PHP_EOL,
                trim(file_get_contents($path)));
            foreach ($lines as $line) {
                if (Str::startsWith($line, $key)) {
                    $parts = explode("=", trim($line));
                    if (count($parts) == 2) {
                        return $parts[1];
                    }
                }
            }
        }
        return null;
    }

    public static function isMultiLangSystem(): bool
    {
        return count(static::getEnabledLocalesFromEnvFile()) > 1;
    }

    public static function isRTLSystem(): bool
    {
        return in_array(app()->getLocale(), static::getRTLLocales());
    }

    public static function getRTLLocales(): array
    {
        return config("translation.rtl_locales");
    }
}
