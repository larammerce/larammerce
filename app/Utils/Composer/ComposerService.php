<?php

namespace App\Utils\Composer;

use Exception;
use Illuminate\Support\Str;

class ComposerService
{
    private static array $DATA = [];
    private static bool $IS_INITIATED = false;

    public static function initiate(): void
    {
        try {
            static::$DATA = json_decode(file_get_contents(base_path("composer.json")), true) ?? [];
            static::$IS_INITIATED = true;
        } catch (Exception $e) {
            static::$DATA = [];
            static::$IS_INITIATED = false;
        }
    }

    public static function getData(): array
    {
        if (!static::$IS_INITIATED)
            static::initiate();
        return static::$DATA;
    }

    public static function getAutoLoad(): array
    {
        return static::getData()["autoload"] ?? [];
    }

    public static function getPSR4(): array
    {
        return static::getAutoLoad()["psr-4"] ?? [];
    }

    /**
     * @throws NotValidComposerAutoloadPathException
     */
    public static function getBasePathFromFQCN(string $fqcn): string
    {
        foreach (static::getPSR4() as $namespace => $base_path) {
            if (Str::startsWith($fqcn, $namespace)) {
                return $base_path;
            }
        }
        throw new NotValidComposerAutoloadPathException("$fqcn is not valid according to current composer.json");
    }

    /**
     * @throws NotValidComposerAutoloadPathException
     */
    public static function getFilePathFromFQCN(string $fqcn): string
    {
        foreach (static::getPSR4() as $namespace => $base_path) {
            if (Str::startsWith($fqcn, $namespace)) {
                return base_path(str_replace([$namespace, "\\"], [$base_path, "/"], $fqcn) . ".php");
            }
        }
        throw new NotValidComposerAutoloadPathException("$fqcn is not valid according to current composer.json");
    }
}
