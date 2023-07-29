<?php

namespace App\Libraries\Excel\Exceptions;

use App\Libraries\Excel\Validators\Failure;
use Exception;
use Illuminate\Support\Collection;

class RowSkippedException extends Exception
{
    /**
     * @var \App\Libraries\Excel\Validators\Failure[]
     */
    private $failures;

    /**
     * @param  \App\Libraries\Excel\Validators\Failure  ...$failures
     */
    public function __construct(Failure ...$failures)
    {
        $this->failures = $failures;

        parent::__construct();
    }

    /**
     * @return \App\Libraries\Excel\Validators\Failure[]|Collection
     */
    public function failures(): Collection
    {
        return new Collection($this->failures);
    }

    /**
     * @return int[]
     */
    public function skippedRows(): array
    {
        return $this->failures()->map->row()->all();
    }
}
