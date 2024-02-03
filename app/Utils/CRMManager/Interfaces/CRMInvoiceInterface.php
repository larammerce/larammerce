<?php

namespace App\Utils\CRMManager\Interfaces;

use Carbon\Carbon;

interface CRMInvoiceInterface {
    public function crmGetInvoiceId();

    public function crmSetInvoiceId(string $invoice_id);

    public function crmGetInvoiceName(): string;

    /**
     * @return array<CRMLineItemInterface>
     */
    public function crmGetLineItems(): array;

    public function crmGetInvoiceAmount(): float;

    public function crmGetInvoiceCreatedAt(): Carbon;

    public function crmGetInvoiceUpdatedAt(): Carbon;

    public function crmSetInvoiceRelCreatedAt(Carbon $created_at): void;

    public function crmGetInvoiceRelCreatedAt(): Carbon;

    public function crmSetInvoiceRelUpdatedAt(Carbon $updated_at): void;

    public function crmGetInvoiceRelUpdatedAt(): Carbon;

    public function crmGetAccountId(): string;
}
