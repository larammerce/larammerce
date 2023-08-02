<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/20/17
 * Time: 9:44 PM
 */

namespace App\Utils\CMS\Enums;


use App\Common\BaseEnum;

class SystemMessageType extends BaseEnum
{
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const ERROR = 'error';
    const INFO = 'info';
}
