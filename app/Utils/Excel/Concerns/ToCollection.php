<?php

namespace App\Utils\Excel\Concerns;

use Illuminate\Support\Collection;

interface ToCollection
{
    /**
     * @param  Collection  $collection
     */
    public function collection(Collection $collection);
}
