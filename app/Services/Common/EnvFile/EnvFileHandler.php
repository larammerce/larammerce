<?php

namespace App\Services\Common\EnvFile;

use App\Interfaces\FileHandlerInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class EnvFileHandler implements FileHandlerInterface {
    public function read(string $path): Collection {
        $file_content = File::get($path);
        $file_lines = collect(explode("\n", $file_content));

        return $file_lines->filter(function ($line) {
            return str_contains($line, '=');
        })->mapWithKeys(function ($line) {
            [$key, $value] = explode('=', $line, 2);
            return [$key => $value];
        });
    }

    public function write(string $path, Collection $lines): void {
        $content = $lines->implode("\n");
        File::put($path, $content);
    }
}
