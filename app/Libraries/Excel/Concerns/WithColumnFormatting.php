<?php

namespace App\Libraries\Excel\Concerns;

interface WithColumnFormatting
{
    /**
     * @return array
     */
    public function columnFormats(): array;
}
