<?php

namespace App\Libraries\Excel\Concerns;

interface WithMappedCells
{
    /**
     * @return array
     */
    public function mapping(): array;
}
