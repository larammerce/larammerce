<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 2/21/19
 * Time: 4:35 PM
 */

namespace App\Models\Enums;


use App\Utils\Common\BaseEnum;

class DirectoryType extends BaseEnum
{
    const REAL=1;
    const BLOG=2;
    const PRODUCT=3;
}