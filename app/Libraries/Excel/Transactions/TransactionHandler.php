<?php

namespace App\Libraries\Excel\Transactions;

interface TransactionHandler
{
    /**
     * @param  callable  $callback
     * @return mixed
     */
    public function __invoke(callable $callback);
}
