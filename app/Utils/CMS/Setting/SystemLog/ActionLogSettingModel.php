<?php


namespace App\Utils\CMS\Setting\SystemLog;


use App\Interfaces\SettingDataInterface;
use App\Utils\Reflection\ReflectiveNamespace;
use JetBrains\PhpStorm\ArrayShape;

class ActionLogSettingModel implements SettingDataInterface
{
    private int $is_enabled;
    private int $log_period;
    private array $enabled_controllers;

    public function __construct()
    {
        $this->is_enabled = 0;
        $this->log_period = 30;             //days
        $this->enabled_controllers = [];
    }


    public function getIsEnabled(): bool
    {
        return $this->is_enabled;
    }

    public function getLogPeriod(): int
    {
        return $this->log_period;
    }

    public function getEnabledControllers(): array
    {
        return $this->enabled_controllers;
    }

    public function setIsEnabled(bool $is_enabled): void
    {
        $this->is_enabled = $is_enabled;
    }

    public function setLogPeriod(int $log_period): void
    {
        $this->log_period = $log_period;
    }

    public function setEnabledControllers(array $enabled_controllers): void
    {
        $this->enabled_controllers = $enabled_controllers;
    }

    public function serialize(): bool|string|null
    {
        return json_encode($this);
    }

    public function unserialize(string $data): void
    {
        $tmp_data = json_decode($data, true);
        $this->is_enabled = $tmp_data["is_enabled"];
        $this->log_period = $tmp_data["log_period"];
        $this->enabled_controllers = $tmp_data["enabled_controllers"];

    }

    public function validate(): bool
    {
        return $this->isValidIsEnabled($this->is_enabled) and
            $this->isValidLogPeriod($this->log_period) and
            $this->areValidControllers($this->enabled_controllers);
    }

    private function isValidIsEnabled($value): bool
    {
        return is_bool($value) or (is_int($value) and ($value === 1 or $value === 0));
    }

    private function isValidLogPeriod($value): bool
    {
        return is_integer($value) and $value > 0;
    }

    private function areValidControllers($controllers): bool
    {
        $existing_controllers = (new ReflectiveNamespace("\\App\\Http\\Controllers\\Admin"))->getClassNames();
        foreach ($controllers as $controller) {
            if (!is_string($controller) or !in_array($controller, $existing_controllers)) {
                return false;
            }
        }
        return true;
    }


    public function getPrimaryKey(): string
    {
        return "";
    }

    #[ArrayShape(["is_enabled" => "int", "log_period" => "int", "enabled_controllers" => "array"])]
    public function jsonSerialize(): array
    {
        return [
            "is_enabled" => $this->is_enabled,
            "log_period" => $this->log_period,
            "enabled_controllers" => $this->enabled_controllers,
        ];
    }

}
