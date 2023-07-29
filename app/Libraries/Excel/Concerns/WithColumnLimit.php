<?php

namespace App\Libraries\Excel\Concerns;

interface WithColumnLimit
{
    public function endColumn(): string;
}
