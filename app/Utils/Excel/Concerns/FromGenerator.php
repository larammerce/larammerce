<?php

namespace App\Utils\Excel\Concerns;

use Generator;

interface FromGenerator
{
    /**
     * @return Generator
     */
    public function generator(): Generator;
}
