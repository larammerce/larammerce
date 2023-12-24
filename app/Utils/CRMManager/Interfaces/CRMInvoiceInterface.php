<?php

namespace App\Utils\CRMManager\Interfaces;

use Carbon\Carbon;

interface CRMInvoiceInterface {
    public function crmGetInvoiceId();

    public function crmSetInvoiceId(string $op_id);

    public function crmGetInvoiceName(): string;

    /**
     * @return array<CRMInvoiceItemInterface>
     */
    public function crmGetInvoiceItems(): array;

    public function crmGetInvoiceAmount(): float;
}
