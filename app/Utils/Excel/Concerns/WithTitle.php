<?php

namespace App\Utils\Excel\Concerns;

interface WithTitle
{
    /**
     * @return string
     */
    public function title(): string;
}
