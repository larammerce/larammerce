<?php


namespace App\Traits;

/**
 * Trait WithDataField
 * @package App\Models\Traits
 */
trait WithDataField
{
    private $extra_attributes = [];

    public function getDataObjectAttribute()
    {
        if (!isset($this->extra_attributes["data_object"]))
            $this->extra_attributes["data_object"] = json_decode($this->data);
        return $this->extra_attributes["data_object"];
    }

    public function setDataObjectAttribute($value)
    {
        $this->extra_attributes["data_object"] = $value;
        $this->data = json_encode($value);
    }
}
