<?php

namespace App\Utils\CMS\Setting\Representative;

use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\AbstractSettingModel;
use App\Utils\CMS\Setting\AbstractSettingService;
use App\Utils\Reflection\AnnotationBadKeyException;
use App\Utils\Reflection\AnnotationBadScopeException;
use App\Utils\Reflection\AnnotationNotFoundException;
use App\Utils\Reflection\AnnotationSyntaxException;
use Illuminate\Support\Str;
use ReflectionException;

class RepresentativeSettingService extends AbstractSettingService
{
    protected static string $KEY_POSTFIX = 'representative';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;
    private static ?RepresentativeSettingModel $CACHED_DATA = null;

    public static function defaultRecord($name): RepresentativeSettingModel
    {
        return new RepresentativeSettingModel();
    }

    /**
     * @throws NotValidSettingRecordException
     */
    public static function update(bool $is_enabled, bool $is_customer_representative_enabled, array $options)
    {
        $new_record = new RepresentativeSettingModel();
        $new_record->setIsEnabled($is_enabled);
        $new_record->setIsCustomerRepresentativeEnabled($is_customer_representative_enabled);
        $new_record->setOptions($options);
        static::setRecord($new_record);
    }

    public static function getRecord(string $name = "", ?string $parent_id = null): ?RepresentativeSettingModel
    {
        if (static::$CACHED_DATA == null)
            static::$CACHED_DATA = parent::getRecord($name, $parent_id);

        return static::$CACHED_DATA;
    }

    public static function isEnabled(): bool
    {
        return static::getRecord()->isEnabled();
    }

    public static function getOptions(): array
    {
        return static::getRecord()->getOptions();
    }

    public static function isCustomerRepresentativeEnabled(): bool
    {
        return static::getRecord()->isCustomerRepresentativeEnabled();
    }
}
