<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/5/17
 * Time: 7:22 PM
 */

namespace App\Utils\CMS\Appliance;

use Illuminate\Support\Str;

/**
 * Class ApplianceObject
 * @package App\Utils\CMS\Appliance
 */
class ApplianceModel
{
    /**
     * @var mixed
     */
    private $applianceFlat;
    /**
     * @var integer
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $icon;
    /**
     * @var string
     */
    private $route;
    /**
     * @var string
     */
    private $url;

    /**
     * ApplianceObject constructor.
     * @param mixed $applianceFlat
     */
    public function __construct($applianceFlat)
    {
        if (key_exists("properties", $applianceFlat)) {
            $this->applianceFlat = $applianceFlat["properties"];
            foreach (array_keys($this->applianceFlat) as $arrayKey) {
                eval("\$this->" . Str::camel("set_" . $arrayKey) . "(" .
                    self::createParameter($this->applianceFlat[$arrayKey]) . ");");
            }
        }
    }

    private static function createParameter($parameter)
    {
        if (gettype($parameter) == "string")
            return "\"${parameter}\"";
        return $parameter;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
        try {
            $this->setUrl(route($this->route));
        } catch (\Exception $e) {
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }


}
