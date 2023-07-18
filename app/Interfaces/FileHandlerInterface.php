<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface FileHandlerInterface {
    public function read(string $path): Collection;
    public function write(string $path, Collection $lines): void;
}