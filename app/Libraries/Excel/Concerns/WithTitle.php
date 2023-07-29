<?php

namespace App\Libraries\Excel\Concerns;

interface WithTitle
{
    /**
     * @return string
     */
    public function title(): string;
}
