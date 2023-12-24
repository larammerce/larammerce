<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/16/18
 * Time: 8:46 PM
 */

namespace App\Utils\CRMManager;

use App\Utils\CRMManager\Interfaces\CRMAccountInterface;
use App\Utils\CRMManager\Interfaces\CRMInvoiceInterface;
use App\Utils\CRMManager\Interfaces\CRMLeadInterface;
use App\Utils\CRMManager\Interfaces\CRMOpportunityInterface;
use App\Utils\CRMManager\Interfaces\CRMPaymentInterface;
use App\Utils\CRMManager\Models\BaseCRMConfig;
use stdClass;

interface BaseDriver {
    public function getId(): string;

    public function getDefaultConfig(): BaseCRMConfig;

    public function authenticate(): bool;

    public function createLead(CRMLeadInterface $lead): bool;

    public function searchLead(CRMLeadInterface $lead): string;

    public function getLeadByRelation(CRMLeadInterface $lead): stdClass;

    public function updateLead(CRMLeadInterface $lead): bool;

    public function createOrUpdateLead(CRMLeadInterface $lead): bool;

    public function createAccount(CRMAccountInterface $account): bool;

    public function createOpportunity(CRMOpportunityInterface $opportunity): bool;

    public function updateOpportunity(CRMOpportunityInterface $opportunity): bool;

    public function createInvoice(CRMInvoiceInterface $invoice);

    public function createPayment(CRMPaymentInterface $payment);
}
