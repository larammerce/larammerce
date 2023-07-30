<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/12/17
 * Time: 3:32 PM
 */

namespace App\Utils\CMS\Setting\Layout;


use App\Interfaces\SettingDataInterface;
use App\Utils\Common\ModelService;
use JetBrains\PhpStorm\ArrayShape;

class LayoutModel implements SettingDataInterface
{
    private string $model;
    private string $method;

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    public function serialize(): bool|string|null
    {
        return json_encode($this);
    }

    public function unserialize($data): void
    {
        $tmpData = json_decode($data);
        $this->model = $tmpData->model;
        $this->method = $tmpData->method;
    }

    #[ArrayShape(['model' => "string", 'method' => "string"])]
    function jsonSerialize(): array
    {
        return [
            'model' => $this->model,
            'method' => $this->method
        ];
    }

    public function validate(): bool
    {
        return ModelService::isValidModel($this->getModel());
    }

    public function getPrimaryKey(): string
    {
        return $this->model;
    }
}
