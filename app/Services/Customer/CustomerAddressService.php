<?php

namespace App\Services\Customer;

use App\Models\CustomerAddress;
use App\Services\Invoice\NewInvoiceService;

class CustomerAddressService
{
    private NewInvoiceService $new_invoice_service;

    public function __construct(NewInvoiceService $new_invoice_service) {
        $this->new_invoice_service = $new_invoice_service;
    }

    public function setAddressAsMain(CustomerAddress $customer_address): bool {
        if (!$customer_address->is_main) {
            $mainAddress = $customer_address->customer->addresses()->main()->first();
            if ($mainAddress != null) {
                $mainAddress->is_main = false;
                $mainAddress->save();
            }
            $customer_address->is_main = true;
            $customer_address->save();
            $customer_address->setAsCurrentLocation();
            $this->new_invoice_service->updateAddress($customer_address);
        }
        return true;
    }
}
