<?php

namespace App\Libraries\Excel\Concerns;

use App\Libraries\Excel\Validators\Failure;
use Illuminate\Support\Collection;

trait SkipsFailures
{
    /**
     * @var \App\Libraries\Excel\Validators\Failure[]
     */
    protected $failures = [];

    /**
     * @param  \App\Libraries\Excel\Validators\Failure  ...$failures
     */
    public function onFailure(Failure ...$failures)
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    /**
     * @return \App\Libraries\Excel\Validators\Failure[]|Collection
     */
    public function failures(): Collection
    {
        return new Collection($this->failures);
    }
}
