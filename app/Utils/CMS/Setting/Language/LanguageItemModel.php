<?php

namespace App\Utils\CMS\Setting\Language;

use App\Traits\Inputable;
use JsonSerializable;
use Serializable;

class LanguageItemModel implements JsonSerializable, Serializable
{
    use Inputable;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public bool $is_enabled;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public bool $is_default;

    public function __construct(bool $is_enabled = false, bool $is_default = false)
    {
        $this->is_enabled = $is_enabled;
        $this->is_default = $is_default;
    }

    public function serialize(): bool|string|null
    {
        return json_encode($this);
    }

    public function unserialize(string $data): void
    {
        $tmp_data = json_decode($data, true);
        foreach ($tmp_data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

}
