<?php

namespace App\Libraries\Excel\Concerns;

interface WithCustomStartCell
{
    /**
     * @return string
     */
    public function startCell(): string;
}
