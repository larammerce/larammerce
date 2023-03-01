<?php

namespace App\Utils\Excel\Concerns;

interface SkipsUnknownSheets
{
    /**
     * @param  string|int  $sheetName
     */
    public function onUnknownSheet($sheetName);
}
