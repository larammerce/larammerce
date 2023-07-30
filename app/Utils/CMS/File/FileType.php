<?php
/**
 * Created by PhpStorm.
 * User: a.morteza
 * Date: 2/23/19
 * Time: 2:25 PM
 */

namespace App\Utils\CMS\File;


use App\Common\BaseEnum;

class FileType extends BaseEnum
{
    //TODO: I thinks there is no need to this enum, because the class name of file types is accessible with ::class directive.
    const DIRECTORY = "App\\Models\\Directory";
    const PRODUCT = "App\\Models\\Product";
    const ARTICLE = "App\\Models\\Article";

}
