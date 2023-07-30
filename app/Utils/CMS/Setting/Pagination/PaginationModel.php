<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/20/17
 * Time: 10:20 PM
 */

namespace App\Utils\CMS\Setting\Pagination;


use App\Interfaces\SettingDataInterface;
use JetBrains\PhpStorm\ArrayShape;

class PaginationModel implements SettingDataInterface
{
    private string $model;
    private int $page;

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model)
    {
        $this->model = $model;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page)
    {
        $this->page = $page;
    }

    public function serialize(): bool|string|null
    {
        return json_encode($this);
    }

    public function unserialize(string $data): void
    {
        $tmpData = json_decode($data);
        $this->model = $tmpData->model;
        $this->page = $tmpData->page;
    }

    public function validate(): bool
    {
        return true;
    }

    public function getPrimaryKey(): string
    {
        return $this->model;
    }

    #[ArrayShape(['model' => "string", 'page' => "int"])]
    function jsonSerialize(): array
    {
        return [
            'model' => $this->model,
            'page' => $this->page
        ];
    }
}
