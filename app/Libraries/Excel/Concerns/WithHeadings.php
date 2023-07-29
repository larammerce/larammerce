<?php

namespace App\Libraries\Excel\Concerns;

interface WithHeadings
{
    /**
     * @return array
     */
    public function headings(): array;
}
