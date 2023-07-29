<?php

namespace App\Libraries\Excel\Concerns;

interface WithUpsertColumns
{
    /**
     * @return array
     */
    public function upsertColumns();
}
