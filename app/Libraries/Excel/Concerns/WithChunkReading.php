<?php

namespace App\Libraries\Excel\Concerns;

interface WithChunkReading
{
    /**
     * @return int
     */
    public function chunkSize(): int;
}
