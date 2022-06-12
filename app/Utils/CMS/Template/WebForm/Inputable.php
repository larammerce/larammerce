<?php


namespace App\Utils\CMS\Template\WebForm;


use Illuminate\Http\Request;

trait Inputable
{
    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return json_encode($this);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $data <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($data)
    {
        $tmpData = json_decode($data);
        $this->name = $tmpData->name;
        $this->title = $tmpData->title;
        $this->rules = $tmpData->rules;
        if (property_exists($this, 'value'))
            $this->value = $tmpData->value;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $data = [
            "name" => $this->name,
            "title" => $this->title,
            "rules" => $this->rules,
        ];
        if (property_exists($this, 'value'))
            $data['value'] = $this->value;
        return $data;

    }

    public function getValidationRules()
    {
        return $this->rules;
    }

    public function getIdentifier()
    {
        return $this->name;
    }

    public function getDisplayTitle()
    {
        return $this->title;
    }

    public function getValue(Request $request)
    {
        return $request->get($this->getIdentifier());
    }
}
