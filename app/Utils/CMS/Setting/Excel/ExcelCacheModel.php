<?php


namespace App\Utils\CMS\Setting\Excel;


use App\Interfaces\SettingDataInterface;
use App\Utils\Reflection\ReflectiveNamespace;
use JetBrains\PhpStorm\ArrayShape;

class ExcelCacheModel implements SettingDataInterface
{

    private array $models;
    private array $model_attributes;
    private array $model_relations;


    public function __construct()
    {
        $this->models = [];
        $this->model_attributes = [];
        $this->model_relations = [];
    }


    public function getModels(): array
    {
        return $this->models;
    }

    public function getModelAttributes(): array
    {
        return $this->model_attributes;
    }

    public function getModelRelations(): array
    {
        return $this->model_relations;
    }

    public function setModels(array $models): void
    {
        $this->models = $models;
    }

    public function setModelAttributes(array $model_attributes): void
    {
        $this->model_attributes = $model_attributes;
    }

    public function setModelRelations(array $model_relations): void
    {
        $this->model_relations = $model_relations;
    }


    public function serialize(): bool|string|null
    {
        return json_encode($this);
    }

    public function unserialize($data): void
    {
        $tmp_data = json_decode($data, true);
        $this->models = $tmp_data["models"];
        $this->model_attributes = $tmp_data["model_attributes"];
        $this->model_relations = $tmp_data["model_relations"];

    }

    public function validate(): bool
    {
        return $this->areValidModels($this->models);
    }


    public function areValidModels(array $models): bool
    {
        $existing_models = (new ReflectiveNamespace("\\App\\Models"))->getClassNames();
        foreach ($models as $model) {
            if (!is_string($model) or !in_array($model, $existing_models)) {
                return false;
            }
        }
        return true;
    }

    public function getPrimaryKey(): string
    {
        return "";
    }

    #[ArrayShape(["models" => "array", "model_attributes" => "array", "model_relations" => "array"])]
    public function jsonSerialize(): array
    {
        return [
            "models" => $this->models,
            "model_attributes" => $this->model_attributes,
            "model_relations" => $this->model_relations,
        ];
    }

}
