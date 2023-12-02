<?php
/**
 * Created by PhpStorm.
 * User: amirhosein
 * Date: 1/23/19
 * Time: 3:47 PM
 */

namespace App\Utils\CRMManager\Drivers\Sarv;

use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CRMManager\BaseDriver;
use App\Utils\CRMManager\ConfigProvider;
use App\Utils\CRMManager\Interfaces\CRMAccountInterface;
use App\Utils\CRMManager\Interfaces\CRMBasePersonInterface;
use App\Utils\CRMManager\Interfaces\CRMLeadInterface;
use App\Utils\CRMManager\Models\BaseCRMConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use stdClass;

class Driver implements BaseDriver {
    const DRIVER_ID = "sarv";

    public function getId(): string {
        return self::DRIVER_ID;
    }

    public function getDefaultConfig(): BaseCRMConfig {
        return new Config();
    }

    public function authenticate(): bool {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $token_created_at = Carbon::parse($config->token_created_at);

        if (!is_null($config->token_created_at) and $token_created_at->addMinutes($config->token_expiration_minutes)->isFuture()) {
            return true;
        }

        $responseV5 = ConnectionFactory::createV5("/API.php?method=Login", $config)
            ->withData(
                [
                    "utype" => $config->utype,
                    "user_name" => $config->username,
                    "password" => md5($config->password),
                    "language" => "en_US",
                ]
            )->asJson()
            ->post();

        $responseV4 = ConnectionFactory::createV4("/service2/v4_1/rest.php?utype={$config->utype}", $config)
            ->withData([
                "method" => "login",
                "input_type" => "JSON",
                "response_type" => "JSON",
                "rest_data" => [
                    "user_auth" => [
                        "user_name" => $config->username,
                        "password" => md5($config->password)
                    ],
                    "name_value_list" => [
                        "language" => [
                            "name" => "language",
                            "value" => "fa_IR"
                        ],
                        "meta_data" => true,
                    ],
                ]
            ])
            ->asJson()
            ->post();

        if ($responseV5->status != 200 or $responseV4->server_response_status != "200") {
            Log::error("Sarv authentication failed: " . json_encode($responseV5));
            return false;
        }

        try {
            $config->token = $responseV5->data?->token;
            $config->session_id = $responseV4->id;
            $config->token_created_at = Carbon::now()->format("Y-m-d H:i:s");
            ConfigProvider::setConfig(self::DRIVER_ID, $config);
            return true;
        } catch (NotValidSettingRecordException $e) {
            return false;
        }
    }

    public function createLead(CRMLeadInterface $lead): bool {
        $this->authenticate();
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $numbers = $this->buildPhoneNumbersList($lead);

        $response = ConnectionFactory::createV5("/API.php?method=Save&module=Leads", $config)
            ->withData(
                [
                    "first_name" => $lead->crmGetFirstName(),
                    "last_name" => $lead->crmGetLastName(),
                    "type" => $lead->crmGetPersonType(),
                    "lead_source" => $lead->crmGetSource(),
                    "status" => $lead->crmGetCreatedAt()->addMonth()->isFuture() ? "New" : "Old",
                    "numbers" => $numbers,
                    "email1" => $lead->crmGetEmail(),
                ]
            )
            ->asJson()
            ->post();

        if ($response->status != 200) {
            Log::error("Sarv create lead failed: " . json_encode($response));
            return false;
        }

        $lead->crmSetLeadId($response->data->id);
        return true;
    }

    public function searchLead(CRMLeadInterface $lead): string {
        $this->authenticate();
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $numbers = $this->buildPhoneNumbersList($lead);

        $response = ConnectionFactory::createV4("/service2/v4_1/rest.php?utype={$config->utype}", $config)
            ->withData(
                [
                    "method" => "get_entry_list",
                    "input_type" => "JSON",
                    "response_type" => "JSON",
                    "rest_data" => [
                        "session" => $config->session_id,
                        "module_name" => "Leads",
                        "query" => "leads.primary_number_raw IN ('" . implode("','", $numbers) . "')",
                    ],
                ]
            )
            ->asJson()
            ->post();

        if ($response->server_response_status != "200" or count($response->entry_list) == 0) {
            Log::error("Sarv search lead failed: " . json_encode($response));
            return "";
        }

        $relation = $response->entry_list[0]->id;
        $lead->crmSetLeadId($relation);

        return $relation;
    }

    public function getLeadByRelation(CRMLeadInterface $lead): stdClass {
        $this->authenticate();
        $config = ConfigProvider::getConfig(self::DRIVER_ID);

        $response = ConnectionFactory::createV5("/API.php?method=Retrieve&module=Leads&id={$lead->crmGetLeadId()}", $config)
            ->asJson()
            ->get();

        if ($response->status != 200) {
            Log::error("Sarv get lead failed: " . json_encode($response));
            return new stdClass();
        }

        return $response->data;
    }

    public function updateLead(CRMLeadInterface $lead): bool {
        $this->authenticate();
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $numbers = $this->buildPhoneNumbersList($lead);

        $response = ConnectionFactory::createV5("/API.php?method=Save&module=Leads&id={$lead->crmGetLeadId()}", $config)
            ->withData(
                [
                    "first_name" => $lead->crmGetFirstName(),
                    "last_name" => $lead->crmGetLastName(),
                    "type" => $lead->crmGetPersonType(),
                    "lead_source" => $lead->crmGetSource(),
                    "status" => $lead->crmGetCreatedAt()->addMonth()->isFuture() ? "New" : "Old",
                    "numbers" => $numbers,
                    "email1" => $lead->crmGetEmail(),
                ]
            )
            ->asJson()
            ->put();

        if ($response->status != 200) {
            Log::error("Sarv update lead failed: " . json_encode($response));
            return false;
        }

        return true;
    }

    public function createOrUpdateLead(CRMLeadInterface $lead): bool {
        $relation = $lead->crmGetLeadId();
        if ($relation == "") {
            $relation = $this->searchLead($lead);
            if ($relation !== "") {
                $lead->crmSetLeadId($relation);
            }
        }

        if ($relation != "") {
            return $this->updateLead($lead);
        } else {
            return $this->createLead($lead);
        }
    }

    public function createAccount(CRMAccountInterface $account): bool {
        $this->authenticate();
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $numbers = $this->buildPhoneNumbersList($account, true);

        $response = ConnectionFactory::createV4("/service2/v4_1/rest.php?utype={$config->utype}", $config)
            ->withData(
                [
                    "method" => "set_entry",
                    "input_type" => "JSON",
                    "response_type" => "JSON",
                    "rest_data" => [
                        "session" => $config->session_id,
                        "module_name" => "Accounts",
                        "name_value_list" => [
                            "name" => $account->crmGetFullName(),
                            "type" => "Customer",
                            "first_name" => $account->crmGetFirstName(),
                            "last_name" => $account->crmGetLastName(),
                            "salutation" => $account->crmGetFullName(),
                            "account_name" => $account->crmGetFullName(),
                            "assigned_user_id" => $config->username,
                            "email1" => $account->crmGetEmail(),
                            "numbers" => $numbers
                        ],
                        "meta_data" => true,
                    ],
                ]
            )
            ->asJson()
            ->post();

        if ($response->server_response_status != "200" or is_null($response->id)) {
            Log::error("Sarv create account has failed. " . json_encode($response));
            return "";
        }
        $relation = $response->id;
        $account->crmSetAccountId($relation);

        return $relation;
    }

    /**
     * @param CRMBasePersonInterface $base_person
     * @param bool $separated
     * @return array<int, string>|array<int, array<string, string>>
     */
    private function buildPhoneNumbersList(CRMBasePersonInterface $base_person, bool $separated = false): array {
        $numbers = [$base_person->crmGetMainPhone()];
        if ($base_person->crmHasSecondaryPhone()) {
            $numbers[] = $base_person->crmGetSecondaryPhone();
        }

        if ($separated) {
            return array_map(function ($number_str) {
                $is_mobile = strlen($number_str) == 11;
                return [
                    "id" => "",
                    "type" => "Mobile",
                    "phonecode" => "+98",
                    "phoneflag" => "IR",
                    "extension" => "",
                    "primary" => $is_mobile,
                    "fax" => "0",
                    "sms" => $is_mobile ? "1" : "0",
                    "number" => $number_str
                ];
            }, $numbers);
        } else {
            return array_map(function ($number_str) {
                if (str_starts_with($number_str, "0")) {
                    return "+98" . substr($number_str, 1);
                } else {
                    return $number_str;
                }
            }, $numbers);
        }
    }
}
