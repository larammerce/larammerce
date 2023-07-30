<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 8/9/17
 * Time: 9:31 PM
 */

namespace App\Utils\CMS\Setting\Sort;

use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Enums\SortMethod;
use App\Utils\CMS\Setting\BaseCMSConfigManager;
use App\Utils\Common\ModelService;
use stdClass;

/**
 * @method static SortModel getRecord(string $name = "", ?string $parent_id = null)
 */
class SortService extends BaseCMSConfigManager
{
    protected static string $KEY_POSTFIX = '_sort_attributes';
    protected static int $SETTING_TYPE = SettingType::LOCAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    protected static function validateName($name): bool
    {
        return ModelService::isValidModel($name);
    }

    public static function defaultRecord($name): SortModel
    {
        $default = new SortModel();
        $default->setModelName(ModelService::className($name));
        $default->setField('id');
        $default->setMethod(SortMethod::ASCENDING);

        return $default;
    }

    public static function getSortableFields($name)
    {
        $current = self::getRecord($name);
        if (static::validateName($name)) {
            $sortable_fields = null;
            $name = ModelService::model($name);
            eval("\$sortable_fields = ${name}::getSortableFields();");
            return array_map(function ($sortableField) use ($current) {
                $result = new stdClass();
                $result->is_active = $current->getField() == $sortableField;
                $result->title = trans('structures.attributes.' . $sortableField);
                $result->field = $sortableField;
                $result->method = $result->is_active ? self::getOppositeMethod($current->getMethod()) : SortMethod::ASCENDING;
                return $result;
            }, $sortable_fields);
        }
        die("Model Not Valid ! [getting sortable fields of '${name}']");
    }

    public static function getOppositeMethod($sortMethod): string
    {
        return $sortMethod == SortMethod::ASCENDING ? SortMethod::DESCENDING : SortMethod::ASCENDING;
    }
}
