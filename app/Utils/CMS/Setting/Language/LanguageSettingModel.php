<?php

namespace App\Utils\CMS\Setting\Language;

use App\Interfaces\SettingDataInterface;
use JetBrains\PhpStorm\ArrayShape;

class LanguageSettingModel implements SettingDataInterface
{
    private LanguageItemModel $config;
    private string $lang_id;

    public function __construct(string $lang_id, bool $is_enabled, bool $is_default)
    {
        $this->config = new LanguageItemModel($is_enabled, $is_default);
        $this->lang_id = $lang_id;
    }

    public function getConfig(): LanguageItemModel
    {
        return $this->config;
    }

    public function setConfig(LanguageItemModel $model)
    {
        $this->config = $model;
    }

    public function getId(): string
    {
        return $this->lang_id;
    }

    public function setId(string $lang_id)
    {
        $this->lang_id = $lang_id;
    }

    public function serialize(): bool|string|null
    {
        return json_encode($this);
    }

    public function unserialize($data)
    {
        $tmp_data = json_decode($data);
        $this->config = unserialize($tmp_data->config);
        $this->lang_id = $tmp_data->lang_id;
    }

    #[ArrayShape(['config' => "string", 'lang_id' => "string"])]
    public function jsonSerialize(): array
    {
        return [
            "config" => serialize($this->config),
            "lang_id" => $this->lang_id
        ];
    }

    public function validate(): bool
    {
        return in_array($this->lang_id, config("translation.available_locales"));
    }

    public function getPrimaryKey(): string
    {
        return $this->lang_id;
    }
}
