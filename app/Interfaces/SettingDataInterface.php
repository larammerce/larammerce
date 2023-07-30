<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/14/17
 * Time: 6:33 PM
 */

namespace App\Interfaces;


use JsonSerializable;
use Serializable;

interface SettingDataInterface extends Serializable, JsonSerializable
{
    public function validate(): bool;

    public function getPrimaryKey(): string;
}
