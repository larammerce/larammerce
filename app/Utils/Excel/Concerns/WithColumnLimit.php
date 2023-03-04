<?php

namespace App\Utils\Excel\Concerns;

interface WithColumnLimit
{
    public function endColumn(): string;
}
