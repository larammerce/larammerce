<?php

namespace App\Libraries\Excel\Concerns;

interface WithEvents
{
    /**
     * @return array
     */
    public function registerEvents(): array;
}
