<?php

namespace App\Libraries\Excel\Concerns;

use Illuminate\Support\Collection;

interface FromCollection
{
    /**
     * @return Collection
     */
    public function collection();
}
