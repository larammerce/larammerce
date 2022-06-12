<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 10/8/17
 * Time: 12:23 PM
 */

namespace App\Utils\CMS\Template\Contents;


use App\Utils\CMS\Template\ContentTypes;

class File extends Content
{

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $src;

    /**
     * File constructor.
     * @param string $id
     * @param string $title
     * @param string $name
     * @param string $src
     */
    public function __construct($id, $title, $name, $src)
    {
        $this->id = $id;
        $this->title = $title;
        $this->name = $name;
        $this->src = $src;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setTitle($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * @param string $src
     */
    public function setSrc($src)
    {
        $this->src = $src;
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
        $this->id = $tmpData->id;
        $this->title = $tmpData->title;
        $this->name = $tmpData->name;
        $this->src = $tmpData->src;
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
            "id" => $this->id,
            "title" => $this->title,
            "name" => $this->name,
            "src" => $this->src
        ];
    }

    public function getType()
    {
        return ContentTypes::FILE;
    }
}
