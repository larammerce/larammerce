<?php

namespace App\Utils\Excel\Concerns;

interface WithUpserts
{
    /**
     * @return string|array
     */
    public function uniqueBy();
}
