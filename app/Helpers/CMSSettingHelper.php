<?php

namespace App\Helpers;

use App\Exceptions\Setting\CMSRecordNotFoundException;
use App\Interfaces\Repositories\SettingRepositoryInterface;

class CMSSettingHelper
{
    private SettingRepositoryInterface $setting_repository;

    public function __construct(SettingRepositoryInterface $setting_repository) {
        $this->setting_repository = $setting_repository;
    }

    public function getCMSSettingAsBool(string $key): bool {
        try {
            $value = $this->setting_repository->getCMSRecord($key)->value;
        } catch (CMSRecordNotFoundException $e) {
            return false;
        }
        $value = strtolower($value);
        if (strlen($value) == 1) {
            return $value == "1";
        } else {
            return $value == "true";
        }
    }

    public function getCMSSettingAsInt(string $key): int {
        try {
            return intval($this->setting_repository->getCMSRecord($key)->value);
        } catch (CMSRecordNotFoundException $e) {
            return 0;
        }
    }

    public function getCMSSettingAsString(string $key): string {
        try {
            return $this->setting_repository->getCMSRecord($key)->value;
        } catch (CMSRecordNotFoundException $e) {
            return "";
        }
    }

    public function getCMSSettingAsArray(string $key): array {
        try {
            return json_decode($this->setting_repository->getCMSRecord($key)->value, true);
        } catch (CMSRecordNotFoundException $e) {
            return [];
        }
    }

    public function getCMSSettingAsObject(string $key): object {
        try {
            return json_decode($this->setting_repository->getCMSRecord($key)->value);
        } catch (CMSRecordNotFoundException $e) {
            return (object)[];
        }
    }

    public function getCMSSettingAsFloat(string $key): float {
        try {
            return floatval($this->setting_repository->getCMSRecord($key)->value);
        } catch (CMSRecordNotFoundException $e) {
            return 0.0;
        }
    }

    public function getCMSSettingAsDouble(string $key): float {
        try {
            return doubleval($this->setting_repository->getCMSRecord($key)->value);
        } catch (CMSRecordNotFoundException $e) {
            return 0.0;
        }
    }
}
