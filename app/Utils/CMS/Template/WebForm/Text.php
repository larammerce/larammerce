<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 12/19/17
 * Time: 7:43 PM
 */

namespace App\Utils\CMS\Template\WebForm;


class Text extends FormField
{
    use Inputable;
    private $value;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        if (key_exists('value', $attributes))
            $this->value = $attributes['value'];

    }

    public static function getTag()
    {
        return 'input';
    }
}