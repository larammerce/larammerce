<?php

namespace App\Utils\CRMManager\Interfaces;

use Carbon\Carbon;

interface CRMLeadInterface extends CRMBasePersonInterface {
    function crmGetLeadId(): string;

    function crmSetLeadId(string $lead_id): void;
}
