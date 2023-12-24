<?php

namespace App\Services\CRMService;

use App\Models\CustomerUser;
use App\Models\Invoice;

class CRMService {
    public function __construct() {
    }

    public function customerCartUpdated(CustomerUser $customer_user): void {
        $customer_user->crm_must_push_op = true;
        $customer_user->save();
    }

    public function customerInvoiceCreated(CustomerUser $customer_user, Invoice $invoice): void {
        if (is_null($customer_user->crm_op_id)) {
            return;
        }

        $invoice->update([
            "crm_op_id" => $customer_user->crm_op_id,
            "crm_op_created_at" => $customer_user->crm_op_created_at,
            "crm_op_updated_at" => $customer_user->crm_op_updated_at
        ]);

        $customer_user->update([
            "crm_must_push_op" => false,
            "crm_op_id" => null,
            "crm_op_created_at" => null,
            "crm_op_updated_at" => null
        ]);
    }
}
