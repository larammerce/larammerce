<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/26/18
 * Time: 4:54 PM
 */

namespace App\Utils\PaymentManager\Models;


class FormAttribute extends BaseModel
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $value;

    /**
     * FormAttribute constructor.
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }


}