<?php

namespace App\Utils\Excel\Concerns;

interface WithUpsertColumns
{
    /**
     * @return array
     */
    public function upsertColumns();
}
