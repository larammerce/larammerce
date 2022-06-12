<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 8/12/17
 * Time: 3:41 PM
 */

namespace App\Utils\Common;


use App\Models\BaseModel;
use Exception;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModelService
{
    private static string $namespace = "\\App\\Models\\";

    public static function isValidModelByField($modelName, $field): bool
    {
        try{
            $model = self::model($modelName);
            if (new $model() instanceof BaseModel and
                !Schema::hasColumn(self::getTableName($modelName), $field)){
                return false;
            }
        } catch (Exception $e){
            return false;
        }
        return true;
    }

    public static function isValidModel($modelName): bool
    {
        if(class_exists($modelName))
            return true;
        return class_exists(self::$namespace.$modelName);
    }

    public static function getTableName($modelName)
    {
        $model = "";
        eval("\$model = new ".self::model($modelName)."();");
        return $model->getTable();
    }

    public static function className($modelName, $dashCase = false): array|bool|string
    {
        $nameParts = (explode("\\", $modelName));
        $class_name = end($nameParts);
        if ($dashCase) {
            $class_name = Str::snake($class_name);
            $class_name = str_replace('_', '-', $class_name);
        }
        return $class_name;
    }

    public static function model($modelName): bool|string
    {
        if(class_exists(self::$namespace.$modelName))
            return self::$namespace.$modelName;
        if(class_exists($modelName))
            return $modelName;
        return false;
    }
}
