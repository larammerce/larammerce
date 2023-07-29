<?php

namespace App\Libraries\Excel\Concerns;

interface WithMultipleSheets
{
    /**
     * @return array
     */
    public function sheets(): array;
}
