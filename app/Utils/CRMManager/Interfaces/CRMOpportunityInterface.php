<?php

namespace App\Utils\CRMManager\Interfaces;

use Carbon\Carbon;

interface CRMOpportunityInterface {
    public function crmGetOpId();

    public function crmSetOpId(string $op_id);

    public function crmGetOpName(): string;

    /**
     * @return array<CRMOpItemInterface>
     */
    public function crmGetOpItems(): array;

    public function crmGetOpAmount(): float;

    public function crmGetOpCreatedAt(): Carbon;

    public function crmGetOpUpdatedAt(): Carbon;

    public function crmSetOpRelCreatedAt(Carbon $created_at): void;

    public function crmGetOpRelCreatedAt(): Carbon;

    public function crmSetOpRelUpdatedAt(Carbon $updated_at): void;

    public function crmGetOpRelUpdatedAt(): Carbon;

    public function crmGetAccountId(): string;

}
