<?php

namespace App\Utils\CRMManager\Interfaces;

interface CRMLineItemInterface {
    public function crmGetLineItemId(): int;

    public function crmGetLineItemCode(): string;

    public function crmGetLineItemName(): string;

    public function crmGetLineItemQuantity(): float;

    public function crmGetLineItemListPrice(): float;

    public function crmGetLineItemSubTotal(): float;

    public function crmGetLineItemDiscountType(): string;

    public function crmGetLineItemDiscountValue(): float;

    public function crmGetLineItemProductDiscountAmount(): float;

    public function crmGetLineItemProductUnitPrice(): float;

    public function crmGetLineItemVatPercentage(): int;

    public function crmGetLineItemVatAmount(): float;

    public function crmGetLineItemGrandTotal(): float;

}
