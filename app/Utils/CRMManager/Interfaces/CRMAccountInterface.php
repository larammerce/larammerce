<?php

namespace App\Utils\CRMManager\Interfaces;

use Carbon\Carbon;

interface CRMAccountInterface extends CRMBasePersonInterface {
    function crmGetAccountId(): string;

    function crmSetAccountId(string $account_id): void;
}
