<?php

namespace App\Utils\Excel\Concerns;

use App\Utils\Excel\Validators\Failure;

interface SkipsOnFailure
{
    /**
     * @param  Failure[]  $failures
     */
    public function onFailure(Failure ...$failures);
}
