<?php

namespace App\Utils\CRMManager\Interfaces;

interface CRMPaymentInterface {

    function crmGetPaymentId(): string;

    function crmSetPaymentId(string $payment_id): void;
    public function getCustomerCRMId(): string;
    public function getPaymentType(): int;
    public function getDescription(): string;
    public function getAmount(): float;
}
