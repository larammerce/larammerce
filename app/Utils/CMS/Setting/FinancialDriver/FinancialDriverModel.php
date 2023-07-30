<?php

namespace App\Utils\CMS\Setting\FinancialDriver;

use App\Interfaces\SettingDataInterface;
use App\Utils\FinancialManager\Kernel;
use App\Utils\FinancialManager\Models\BaseFinancialConfig;
use JetBrains\PhpStorm\ArrayShape;

class FinancialDriverModel implements SettingDataInterface
{
    private BaseFinancialConfig $config_model;
    private string $driver_id;

    public function hasConfigModel(): bool
    {
        return isset($this->config_model);
    }

    public function getConfigModel(): BaseFinancialConfig
    {
        return $this->config_model;
    }

    public function setConfigModel(BaseFinancialConfig $model)
    {
        $this->config_model = $model;
    }

    public function getId(): string
    {
        return $this->driver_id;
    }

    public function setDriverId(string $driver_id)
    {
        $this->driver_id = $driver_id;
    }

    public function serialize(): bool|string|null
    {
        return json_encode($this);
    }

    public function unserialize($data)
    {
        $tmp_data = json_decode($data);
        $this->config_model = unserialize($tmp_data->config_model);
        $this->driver_id = $tmp_data->driver_id;
    }

    #[ArrayShape(['config_model' => "string", 'driver_id' => "string"])]
    public function jsonSerialize(): array
    {
        return [
            'config_model' => serialize($this->config_model),
            'driver_id' => $this->driver_id
        ];
    }

    public function validate(): bool
    {
        return in_array($this->driver_id, array_keys(Kernel::$drivers));
    }

    public function getPrimaryKey(): string
    {
        return $this->driver_id;
    }
}
