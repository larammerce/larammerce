<?php

namespace App\Libraries\Excel\Concerns;

use App\Libraries\Excel\Row;

interface OnEachRow
{
    /**
     * @param  Row  $row
     */
    public function onRow(Row $row);
}
