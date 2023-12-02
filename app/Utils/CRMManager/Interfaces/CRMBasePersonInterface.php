<?php

namespace App\Utils\CRMManager\Interfaces;

use Carbon\Carbon;

interface CRMBasePersonInterface {

    function crmGetFullName(): string;

    function crmGetFirstName(): string;

    function crmGetLastName(): string;

    function crmGetSource(): string;

    function crmGetMainPhone(): string;

    function crmGetSecondaryPhone(): string;

    function crmHasSecondaryPhone(): bool;

    function crmGetEmail(): string;

    function crmGetCreatedAt(): Carbon;

    function crmGetPersonType(): string;
}
