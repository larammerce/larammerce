<?php

namespace App\Utils\PaymentManager\Models;

use App\Traits\Inputable;
use JsonSerializable;
use Serializable;

abstract class BasePaymentConfig extends BaseModel implements JsonSerializable, Serializable
{
    use Inputable;

    const LOGO_PATH = "/primary_data/payment_drivers/local/logo.png";

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public $is_enabled;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public $is_default;

    public function __construct()
    {
        $this->is_default = false;
        $this->is_enabled = false;
    }

    public function serialize()
    {
        return json_encode($this);
    }

    public function unserialize(string $data)
    {
        $tmp_data = json_decode($data, true);
        foreach($tmp_data as $key=>$value){
            $this->$key = $value;
        }
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getLogoPath(): string
    {
        return $this::LOGO_PATH;
    }
}
