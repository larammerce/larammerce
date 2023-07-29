<?php

namespace App\Libraries\Excel\Concerns;

interface WithBatchInserts
{
    /**
     * @return int
     */
    public function batchSize(): int;
}
