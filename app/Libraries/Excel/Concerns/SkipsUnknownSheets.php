<?php

namespace App\Libraries\Excel\Concerns;

interface SkipsUnknownSheets
{
    /**
     * @param  string|int  $sheetName
     */
    public function onUnknownSheet($sheetName);
}
