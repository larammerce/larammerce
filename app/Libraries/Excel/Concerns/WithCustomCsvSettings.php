<?php

namespace App\Libraries\Excel\Concerns;

interface WithCustomCsvSettings
{
    /**
     * @return array
     */
    public function getCsvSettings(): array;
}
