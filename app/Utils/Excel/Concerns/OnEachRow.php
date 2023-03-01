<?php

namespace App\Utils\Excel\Concerns;

use App\Utils\Excel\Row;

interface OnEachRow
{
    /**
     * @param  Row  $row
     */
    public function onRow(Row $row);
}
