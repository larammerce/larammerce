<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/26/18
 * Time: 4:54 PM
 */

namespace App\Utils\PaymentManager\Models;


class Form extends BaseModel
{

    /**
     * @var string
     */
    public $method;
    /**
     * @var string
     */
    public $action;
    /**
     * @var FormAttribute[]
     */
    public $attributes;

    /**
     * Form constructor.
     * @param string $action
     * @param string $method
     * @param FormAttribute[] $attributes
     */
    public function __construct($method, $action, array $attributes)
    {
        $this->method = $method;
        $this->action = $action;
        $this->attributes = $attributes;
    }


}