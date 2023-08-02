<?php

namespace App\Utils\CMS\Setting\Representative;

use App\Interfaces\SettingDataInterface;

class RepresentativeSettingModel implements SettingDataInterface {
    private bool $is_enabled;
    private bool $is_customer_representative_enabled;
    private array $options;

    private bool $is_forced;

    public function __construct() {
        $this->is_enabled = false;
        $this->is_forced = false;
        $this->is_customer_representative_enabled = false;
        $this->options = [];
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool {
        return $this->is_enabled;
    }

    /**
     * @param bool $is_enabled
     */
    public function setIsEnabled(bool $is_enabled): void {
        $this->is_enabled = $is_enabled;
    }

    /**
     * @return array
     */
    public function getOptions(): array {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void {
        $this->options = $options;
    }

    /**
     * @return bool
     */
    public function isCustomerRepresentativeEnabled(): bool {
        return $this->is_customer_representative_enabled;
    }

    /**
     * @param bool $is_customer_representative_enabled
     */
    public function setIsCustomerRepresentativeEnabled(bool $is_customer_representative_enabled): void {
        $this->is_customer_representative_enabled = $is_customer_representative_enabled;
    }

    /**
     * @return bool
     */
    public function isForced(): bool {
        return $this->is_forced;
    }

    /**
     * @param bool $is_forced
     */
    public function setIsForced(bool $is_forced): void {
        $this->is_forced = $is_forced;
    }

    public function validate(): bool {
        return count(array_filter($this->options, function ($iter_item) {
                return !is_string($iter_item);
            })) == 0;
    }

    public function getPrimaryKey(): string {
        return "";
    }

    public function serialize(): bool|string|null {
        return json_encode($this);
    }

    public function unserialize(string $data): void {
        $tmp_data = json_decode($data, true);
        $this->options = $tmp_data["options"] ?? [];
        $this->is_enabled = $tmp_data["is_enabled"] ?? false;
        $this->is_forced = $tmp_data["is_forced"] ?? false;
        $this->is_customer_representative_enabled = $tmp_data["is_customer_representative_enabled"] ?? false;
    }

    public function jsonSerialize(): array {
        return [
            "is_enabled" => $this->is_enabled,
            "options" => $this->options,
            "is_forced" => $this->is_forced,
            "is_customer_representative_enabled" => $this->is_customer_representative_enabled
        ];
    }
}
