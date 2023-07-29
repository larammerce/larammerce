<?php

namespace App\Libraries\Migrator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigratorService
{
    private static function mapWithPaths($migration_names): array
    {
        $result = [];
        foreach (static::getAllMigrations(true) as $name => $path) {
            if (in_array($name, $migration_names))
                $result[$name] = $path;
        }
        return $result;
    }

    public static function getAllMigrations($need_path = false): array
    {
        $migrations = [];
        $paths = [database_path("migrations")];
        while (count($paths) > 0) {
            $current_path = array_pop($paths);
            foreach (scandir($current_path) as $item) {
                $item_path = $current_path . "/" . $item;
                if (is_dir($item_path) and !in_array($item, [".", ".."])) {
                    $paths[] = $item_path;
                } else if (Str::contains($item, ".php")) {
                    $migrations[Str::replace(".php", "", $item)] = $item_path;
                }
            }
        }
        return $need_path ? $migrations : array_keys($migrations);
    }

    public static function getDoneMigrations($need_path = false): array
    {
        $done_migrations = DB::table("migrations")->select("migration")->pluck("migration")->toArray();
        if ($need_path) {
            return static::mapWithPaths($done_migrations);
        }
        return $done_migrations;
    }

    public static function getNotDoneMigrations($need_path = false): array
    {
        $not_done_migrations = array_diff(static::getAllMigrations(), static::getDoneMigrations());
        if ($need_path) {
            return static::mapWithPaths($not_done_migrations);
        }
        return $not_done_migrations;
    }
}
