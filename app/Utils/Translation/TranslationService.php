<?php

namespace App\Utils\Translation;

use App\Utils\CMS\Setting\Language\LanguageSettingService;
use App\Utils\Composer\ComposerService;
use App\Utils\Composer\NotValidComposerAutoloadPathException;
use App\Utils\Migrator\MigratorService;
use App\Utils\Reflection\ReflectiveClass;
use App\Utils\Reflection\ReflectiveNamespace;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class TranslationService
{
    private static $KEY_TYPES = [
        "integer" => "Integer",
        "bigint" => "BigInteger"
    ];

    public static function getTranslatableTrait(): string
    {
        return config('translation.translation_trait');
    }

    public static function getTranslationModelsNamespace(): string
    {
        return config("translation.translation_models_namespace");
    }

    public static function getModelsNamespace(): string
    {
        return config("translation.models_namespace");
    }

    public static function getTranslationModelSuffix(): string
    {
        return config("translation.translation_suffix", "Translation");
    }

    public static function getTranslationMigrationsBasePath(): string
    {
        return config("translation.migrations_path");
    }

    public static function getTranslationModels(): array
    {
        try {
            return (new ReflectiveNamespace(static::getTranslationModelsNamespace()))->getReflectiveClasses();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @return ReflectiveClass[]
     */
    public static function getModels(): array
    {
        try {
            return (new ReflectiveNamespace(static::getModelsNamespace()))->getReflectiveClasses();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @return ReflectiveClass[]
     */
    public static function getTranslatableModels(): array
    {
        return array_filter(static::getModels(), function (ReflectiveClass $class) {
            return static::isTranslatable($class);
        });
    }

    public static function isTranslatable(ReflectiveClass $class): bool
    {
        return $class->usesTrait(static::getTranslatableTrait());
    }

    public static function getTranslationModelFromModel(ReflectiveClass $class): string
    {
        return static::getTranslationModelFQCN($class->getReflectionClass()->getShortName());
    }

    public static function getTranslationModelFQCN(string $translation_model_name): string
    {
        return static::getTranslationModelsNamespace() . "\\" . $translation_model_name . static::getTranslationModelSuffix();
    }

    public static function getTranslationTableName(ReflectiveClass $class): string
    {
        return Str::snake(Str::plural($class->getReflectionClass()->getShortName() . static::getTranslationModelSuffix()));
    }

    #[ArrayShape(["type" => "string", "table_name" => "string", "foreign_table_name" => "string", "foreign_key" => "string", "foreign_key_type" => "string", "columns" => "array"])]
    public static function getMigrationData(ReflectiveClass $class): array
    {
        $tmp_model_instance = app($class->getClassName());
        $translation_table_name = static::getTranslationTableName($class);

        $migration_data = [
            "type" => "create",
            "table_name" => $translation_table_name,
            "foreign_table_name" => $tmp_model_instance->getTable(),
            "foreign_key" => $tmp_model_instance->getForeignKey(),
            "foreign_key_type" => static::$KEY_TYPES[Schema::getColumnType($tmp_model_instance->getTable(), $tmp_model_instance->getKeyName())] ?? "",
            "columns" => $tmp_model_instance->getTranslatableFields(with_column_type: true)
        ];

        if (Schema::hasTable($translation_table_name)) {
            $migration_data["type"] = "update";
            $migration_data["columns"] = [];
            foreach ($tmp_model_instance->getTranslatableFields(with_column_type: true) as $column_name => $column_type) {
                if (!Schema::hasColumn($translation_table_name, $column_name)) {
                    $migration_data["columns"][$column_name] = $column_type;
                }
            }
        }

        return $migration_data;
    }

    public static function makeTranslationMigration(ReflectiveClass $class): void
    {
        $migration_data = static::getMigrationData($class);

        if (count($migration_data["columns"]) == 0)
            return;

        foreach (MigratorService::getNotDoneMigrations() as $migration)
            if (Str::contains($migration, $migration_data["table_name"]))
                return;

        $schema = [];
        foreach ($migration_data["columns"] as $name => $type) {
            $schema[] = $name . ":" . $type;
        }
        $schema = implode(", ", $schema);

        $unique_name = "un_lo_fore_" . $migration_data["table_name"];

        if ($migration_data["type"] == "create") {
            $schema = "{$schema}, locale:string:index, {$migration_data["foreign_key"]}:unsigned{$migration_data["foreign_key_type"]}:foreign, '{$migration_data["foreign_key"]}'|'locale':unique('{$unique_name}')";
        }

        Artisan::call("make:migration:schema", [
            "name" => $migration_data["type"] . "_" . $migration_data["table_name"] . "_table",
            "--path" => static::getTranslationMigrationsBasePath(),
            "--schema" => $schema
        ]);
    }

    /**
     * @throws NotValidComposerAutoloadPathException
     */
    public static function makeTranslationModel(ReflectiveClass $class): void
    {
        $translation_model_fqcn = TranslationService::getTranslationModelFromModel($class);
        $translation_model_file_path = ComposerService::getFilePathFromFQCN($translation_model_fqcn);

        if (!file_exists($translation_model_file_path)) {
            Artisan::call('make:model', [
                "name" => $translation_model_fqcn,
            ]);
            static::fixModelContents($class, $translation_model_file_path);
        }
    }


    public static function fixModelContents(ReflectiveClass $class, $file_path): void
    {
        $table_name = static::getTranslationTableName($class);;
        $contents = explode(PHP_EOL,
            trim(file_get_contents($file_path)));
        $contents = array_filter($contents, function ($e) {
            return (!str_contains(trim($e), "HasFactory"));
        });
        $contents = array_values($contents);
        $length = count($contents);
        unset($contents[$length - 1]);
        $contents[] = '      protected $table = "' . "$table_name" . '";';
        $contents[] = '      public $timestamps = false;';
        $contents[] = "}";
        file_put_contents($file_path, implode("\n", $contents));
    }
}
