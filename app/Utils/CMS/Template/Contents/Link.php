<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 10/8/17
 * Time: 12:23 PM
 */

namespace App\Utils\CMS\Template\Contents;


use App\Utils\CMS\Template\ContentTypes;

class Link extends Content
{
    /**
     * @var string
     */
    private $href;
    /**
     * @var string
     */
    private $content;

    /**
     * Link constructor.
     * @param string $id
     * @param string $title
     * @param string $href
     * @param string $content
     */
    public function __construct($id, $title, $href, $content)
    {
        $this->id = $id;
        $this->title = $title;
        $this->href = $href;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @param string $href
     */
    public function setHref($href)
    {
        $this->href = $href;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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
        $this->href = $tmpData->href;
        $this->content = $tmpData->content;
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
            "href" => $this->href,
            "content" => $this->content
        ];
    }

    public function getType()
    {
        return ContentTypes::LINK;
    }
}
