<?php

namespace App\Services\Directory;

use App\Exceptions\Directory\DirectoryNotFoundException;
use App\Models\Directory;
use Exception;

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
}