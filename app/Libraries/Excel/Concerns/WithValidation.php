<?php

namespace App\Libraries\Excel\Concerns;

interface WithValidation
{
    /**
     * @return array
     */
    public function rules(): array;
}
