<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/14/17
 * Time: 6:33 PM
 */

namespace App\Utils\CMS\Setting;


use JsonSerializable;
use Serializable;

abstract class AbstractSettingModel implements Serializable, JsonSerializable
{
    public abstract function validate(): bool;

    public abstract function getPrimaryKey(): string;
}
