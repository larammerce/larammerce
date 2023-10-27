<?php

namespace App\Utils\CRMManager\Interfaces;

use Carbon\Carbon;

interface CRMLeadInterface {
    function leadGetRelation(): string;

    function leadSetRelation(string $lead_id): void;

    function leadGetFullName(): string;

    function leadGetFirstName(): string;

    function leadGetLastName(): string;

    function leadGetSource(): string;

    function leadGetMainPhone(): string;

    function leadGetSecondaryPhone(): string;

    function leadHasSecondaryPhone(): bool;

    function leadGetEmail(): string;

    function leadGetType(): string;

    function leadGetCreatedAt(): Carbon;
}
