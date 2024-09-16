<?php

namespace App\Interfaces\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface InvoiceRepositoryInterface
{
    public function findByQueryAndFilters(Builder $builder,Request $request): Builder;
}
