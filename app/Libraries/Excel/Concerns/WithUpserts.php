<?php

namespace App\Libraries\Excel\Concerns;

interface WithUpserts
{
    /**
     * @return string|array
     */
    public function uniqueBy();
}
