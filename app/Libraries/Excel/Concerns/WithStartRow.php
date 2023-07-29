<?php

namespace App\Libraries\Excel\Concerns;

interface WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int;
}
