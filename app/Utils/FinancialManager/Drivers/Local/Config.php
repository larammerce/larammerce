<?php

namespace App\Utils\FinancialManager\Drivers\Local;

use App\Utils\FinancialManager\Models\BaseFinancialConfig;

class Config extends BaseFinancialConfig
{
    public function __construct()
    {
        parent::__construct();
        $this->is_manual_stock = true;
    }
}
