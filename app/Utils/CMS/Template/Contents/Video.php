<?php

namespace App\Utils\CMS\Template\Contents;


use App\Utils\CMS\Template\ContentTypes;
use JsonSchema\Uri\Retrievers\FileGetContents;

class Video extends Content
{
    /**
     * @var string
     */
    protected $title;
    protected $src;
    protected $format;
    protected $poster;
    protected $controls;
    protected $auto_play;
    protected $loop;

    /**
     * Video constructor.
     * @param integer $id
     * @param string $title
     * @param string $src
     * @param string $format
     * @param string $poster
     * @param boolean $controls
     * @param boolean $auto_play
     * @param boolean $loop
     */
    public function __construct($id, $title, $src, $format, $poster, $controls = true,
                                $auto_play = false, $loop = false)
    {
        $this->id = $id;
        $this->title = $title;
        $this->src = $src;
        $this->format = $format;
        $this->poster = $poster;
        $this->controls = $controls;
        $this->auto_play = $auto_play;
        $this->loop = $loop;

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


    /**
     * @param string $poster
     */
    public function setPoster($poster)
    {
        $this->poster = $poster;
    }

    /**
     * @return string
     */
    public function getPoster()
    {
        return $this->poster;
    }


    /**
     * @param bool $controls
     */
    public function setControls($controls)
    {
        $this->controls = $controls;
    }

    /**
     * @return bool
     */
    public function hasControls()
    {
        return $this->controls == true;
    }

    /**
     * @param bool $auto_play
     */
    public function setAutoPlay($auto_play)
    {
        $this->auto_play = $auto_play;
    }

    /**
     * @return bool
     */
    public function isAutoPlay()
    {
        return $this->auto_play == 1;
    }

    /**
     * @param bool $loop
     */
    public function setLoop($loop)
    {
        $this->loop = $loop;
    }

    /**
     * @return bool
     */
    public function hasLoop()
    {
        return $this->loop == true;
    }

    public function getType()
    {
        return ContentTypes::VIDEO;
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
        $this->poster = $tmpData->poster;
        $this->controls = $tmpData->controls;
        $this->auto_play = $tmpData->auto_play;
        $this->loop = $tmpData->loop;
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
            "format"=> $this->format,
            "poster" => $this->poster,
            "controls" => $this->controls,
            "auto_play" => $this->auto_play,
            "loop" => $this->loop
        ];
    }
}
