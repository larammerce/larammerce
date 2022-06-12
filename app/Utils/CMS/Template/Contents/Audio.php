<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 10/8/17
 * Time: 12:23 PM
 */

namespace App\Utils\CMS\Template\Contents;


use App\Utils\CMS\Template\ContentTypes;
use JsonSchema\Uri\Retrievers\FileGetContents;

class Audio extends Content
{
    /**
     * @var string
     */
    private $src;

    /**
     * @var string
     */
    private $format;

    public function __construct($id, $title, $src, $format)
    {
        $this->id = $id;
        $this->title = $title;
        $this->src = $src;
        $this->format = $format;

    }

    /**
     * @param string $src
     */
    public function setSrc($src)
    {
        $this->src = $src;
    }

    /**
     * @return string
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * @param $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
       return $this->format;
    }

    public function getType()
    {
        return ContentTypes::AUDIO;
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
        $this->src = $tmpData->src;
        $this->format = $tmpData->format;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any format other than a resource.
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "src" => $this->src,
            "format"=> $this->format
        ];
    }
}
