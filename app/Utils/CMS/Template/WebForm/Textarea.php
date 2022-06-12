<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 12/19/17
 * Time: 7:43 PM
 */

namespace App\Utils\CMS\Template\WebForm;


class Textarea extends FormField
{
    use Inputable;

    public static function getTag()
    {
        return'textarea';
    }
}