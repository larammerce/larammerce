<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 12/19/17
 * Time: 7:43 PM
 */

namespace App\Utils\CMS\Template\WebForm;


use Illuminate\Http\Request;

class Select extends FormField
{
    private $options;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        if (key_exists('options', $attributes))
            $this->options = $attributes['options'];

    }

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
        foreach ($tmpData->options as $option) {
            $stdOption = new Option();
            $stdOption->unserialize($option);
            array_push($this->options, $stdOption);
        }
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
        return [
            "name" => $this->name,
            "title" => $this->title,
            "rules" => $this->rules,
            "options" => $this->options
        ];
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
        //TODO: check this
        return $this->options[$request->get($this->getIdentifier())];
    }

    public static function getTag()
    {
        return 'select';
    }
}
