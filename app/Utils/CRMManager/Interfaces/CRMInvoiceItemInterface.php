<?php

namespace App\Utils\CRMManager\Interfaces;

interface CRMInvoiceItemInterface {
    public function crmGetInvoiceItemId(): int;

    public function crmGetInvoiceItemCode(): string;

    public function crmGetInvoiceItemName(): string;

    public function crmGetInvoiceItemPrice(): float;

    public function crmGetInvoiceItemQuantity(): float;

    public function crmGetInvoiceItemAmount(): float;

    public function crmGetInvoiceListPrice(): float;

    public function crmGetInvoiceSubTotal(): float;

    public function crmGetInvoiceDiscountType(): float;

    public function crmGetInvoiceProductDiscountAmount(): float;

    public function crmGetInvoiceProductUnitPrice(): float;

    public function crmGetInvoiceVatPercentage(): int;

    public function crmGetInvoiceVatAmount(): float;

    public function crmGetInvoiceGrandTotal(): float;

}
