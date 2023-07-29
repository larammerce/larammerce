<?php

namespace App\Libraries\Excel\Concerns;

interface WithLimit
{
    /**
     * @return int
     */
    public function limit(): int;
}
