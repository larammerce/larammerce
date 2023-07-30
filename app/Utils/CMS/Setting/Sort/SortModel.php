<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 8/9/17
 * Time: 9:25 PM
 */

namespace App\Utils\CMS\Setting\Sort;


use App\Interfaces\SettingDataInterface;
use App\Utils\Common\ModelService;
use JetBrains\PhpStorm\ArrayShape;

class SortModel implements SettingDataInterface
{
    private string $model_name;
    private string $field;
    private string $method;

    public function getModelName(): string
    {
        return $this->model_name;
    }

    public function setModelName(string $model_name)
    {
        $this->model_name = $model_name;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field)
    {
        $this->field = $field;
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

    public function unserialize(string $data): void
    {
        $tmpData = json_decode($data);
        $this->model_name = $tmpData->model_name;
        $this->field = $tmpData->field;
        $this->method = $tmpData->method;
    }

    #[ArrayShape(["model_name" => "string", "field" => "string", "method" => "string"])]
    function jsonSerialize(): array
    {
        return [
            "model_name" => $this->model_name,
            "field" => $this->field,
            "method" => $this->method
        ];
    }

    public function validate(): bool
    {
        return ModelService::isValidModelByField($this->getModelName(), $this->getField());
    }

    public function getPrimaryKey(): string
    {
        return $this->model_name;
    }
}
