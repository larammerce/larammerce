<?php

namespace App\Services\Directory;

use App\Exceptions\Directory\DirectoryNotFoundException;
use App\Helpers\Common\StringHelper;
use App\Models\Directory;
use Exception;
use Illuminate\Support\Facades\Cache;

class DirectoryService {
    /**
     * @throws DirectoryNotFoundException
     */
    public static function findDirectoryById(int $directory_id): Directory {
        try {
            return Directory::findOrFail($directory_id);
        } catch (Exception $e) {
            throw new DirectoryNotFoundException("The directory with id `{$directory_id}` not found int the database.");
        }
    }

    public static function buildDirectoriesTree(?Directory $root = null, array $conditions = [], array $order = []): array {
        $cache_key = StringHelper::getCacheKey([static::class, __FUNCTION__], $root?->id ?? 0, json_encode($conditions), json_encode($order));

        if(!Cache::has($cache_key)){
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

            Cache::put($cache_key, ($root == null ? $branch : ($map[$root->id]->directories ?? [])), 600);
        }

        return Cache::get($cache_key);
    }
}