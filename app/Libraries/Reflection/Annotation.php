<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/13/16
 * Time: 1:44 PM
 */

namespace App\Libraries\Reflection;


use JsonSerializable;

class Annotation implements JsonSerializable
{
    /**
     * @var string
     */
    private $title;
    /**
     * @var mixed
     */
    private $properties;

    public function __construct($title, $properties = [])
    {
        $this->title = $title;
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param mixed $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function put(string $key, $value): bool
    {
        if (!key_exists($key, $this->properties)) {
            $this->properties[$key] = $value;
            return true;
        } else
            return false;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function drop(string $key): bool
    {
        if (key_exists($key, $this->properties)) {
            unset($this->properties[$key]);
            return true;
        } else
            return false;
    }

    /**
     * @return void
     */
    public function clear()
    {
        $this->properties = [];
    }

    /**
     * @return string[]
     */
    public function getPropertyNames(): array
    {
        return array_keys($this->properties);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getProperty(string $name)
    {
        if ($this->hasProperty($name))
            return $this->properties[$name];
        return false;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasProperty(string $name): bool
    {
        return array_key_exists($name, $this->properties);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function checkProperty(string $key, $value): bool
    {
        return $this->hasProperty($key) and $value == $this->getProperty($key);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return array data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'properties' => $this->properties
        ];
    }
}