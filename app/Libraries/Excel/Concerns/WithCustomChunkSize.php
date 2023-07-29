<?php

namespace App\Libraries\Excel\Concerns;

interface WithCustomChunkSize
{
    /**
     * @return int
     */
    public function chunkSize(): int;
}
