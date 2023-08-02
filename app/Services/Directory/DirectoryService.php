<?php

namespace App\Services\Directory;

use App\Exceptions\Directory\DirectoryNotFoundException;
use App\Models\Directory;
use Exception;
use Illuminate\Support\Collection;

class DirectoryService
{
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

    /**
     * @param Directory[] $directories
     * @param int $parent_id
     * @return array
     */
    public static function buildDirectoryGraph(Collection|array $directories, int $parent_id = 0): array {
        $directories_count = count($directories);
        if ($directories_count == 0)
            return [];

        if ($parent_id === 0) {
            $roots = [];
            $root_ids = [];
            for ($i = 0; $i < count($directories) and !in_array($directories->get($i)->directory_id, $root_ids); $i++) {
                $root_directory = $directories->get($i);
                $root_directory->child_nodes = static::buildDirectoryGraph($directories, $root_directory->id);
                $roots[] = $root_directory;
                $root_ids[] = $root_directory->id;
            }

            return $roots;
        } else {
            $children = [];

            for ($i = 0; $i < count($directories); $i++) {
                $directory = $directories->get($i);
                if ($directory->directory_id === $parent_id) {
                    $directory->child_nodes = static::buildDirectoryGraph($directories, $directory->id);
                    $children[] = $directory;
                }
            }

            return $children;
        }
    }
}
