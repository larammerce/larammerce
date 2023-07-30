<?php


namespace App\Utils\CMS\Setting\CartNotification;

use App\Interfaces\SettingDataInterface;
use JetBrains\PhpStorm\ArrayShape;

class CartNotificationModel implements SettingDataInterface
{
    private int $default_delay_hours;
    private bool $is_active;
    private bool $notify_with_email;
    private bool $notify_with_sms;

    public function __construct()
    {
        $this->default_delay_hours = 0;
        $this->is_active = false;
        $this->notify_with_email = false;
        $this->notify_with_sms = false;
    }

    public function getDefaultDelayHours(): int
    {
        return $this->default_delay_hours;
    }

    public function setDefaultDelayHours(int $default_delay_hours): void
    {
        $this->default_delay_hours = $default_delay_hours;
    }

    public function getIsActive(): bool
    {
        return $this->is_active;
    }

    public function setIsActive(int $is_active): void
    {
        $this->is_active = $is_active;
    }

    public function getNotifyWithEmail(): bool
    {
        return $this->notify_with_email;
    }

    public function setNotifyWithEmail(string $notify_with_email): void
    {
        $this->notify_with_email = $notify_with_email;
    }

    public function getNotifyWithSMS(): bool
    {
        return $this->notify_with_sms;
    }

    public function setNotifyWithSMS(string $notify_with_sms): void
    {
        $this->notify_with_sms = $notify_with_sms;
    }

    public function serialize(): bool|string|null
    {
        return json_encode($this);
    }

    public function unserialize(string $data): void
    {
        $tmp_data = json_decode($data, true);
        $this->is_active = $tmp_data["is_active"];
        $this->default_delay_hours = $tmp_data["default_delay_hours"];
        $this->notify_with_email = $tmp_data["notify_with_email"];
        $this->notify_with_sms = $tmp_data["notify_with_sms"];
    }

    public function validate(): bool
    {
        return $this->isValidHours($this->default_delay_hours);
    }

    private function isValidHours($hours): bool
    {
        return is_integer($hours) and $hours >= 0 and $hours <= 24;
    }

    public function getPrimaryKey(): string
    {
        return "";
    }

    #[ArrayShape(["is_active" => "bool", "default_delay_hours" => "int", "notify_with_email" => "bool", "notify_with_sms" => "bool"])]
    public function jsonSerialize(): array
    {
        return [
            "is_active" => $this->is_active,
            "default_delay_hours" => $this->default_delay_hours,
            "notify_with_email" => $this->notify_with_email,
            "notify_with_sms" => $this->notify_with_sms
        ];
    }
}
