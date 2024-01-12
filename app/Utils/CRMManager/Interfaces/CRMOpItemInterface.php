<?php

namespace App\Utils\CRMManager\Interfaces;

interface CRMOpItemInterface {
    public function crmGetOpItemId(): int;

    public function crmGetOpItemCode(): string;

    public function crmGetOpItemName(): string;

    public function crmGetOpItemPrice(): float;

    public function crmGetOpItemQuantity(): float;

    public function crmGetOpItemAmount(): float;

    public function crmGetOpListPrice(): float;

    public function crmGetOpSubTotal(): float;

    public function crmGetOpDiscountType(): string;

    public function crmGetOpDiscountValue(): float;

    public function crmGetOpProductDiscountAmount(): float;

    public function crmGetOpProductUnitPrice(): float;

    public function crmGetOpVatPercentage(): int;

    public function crmGetOpVatAmount(): float;

    public function crmGetOpGrandTotal(): float;

}
