<?php

namespace App\Utils\CRMManager\Models;

use App\Traits\Inputable;
use JsonSerializable;
use Serializable;

abstract class BaseCRMConfig extends BaseModel implements JsonSerializable, Serializable {
    use Inputable;

    /**
     * @rules(input_rule="string")
     * @data(input_type="text")
     */
    public $host;

    /**
     * @rules(input_rule="integer")
     * @data(input_type="number")
     */
    public $port;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public $ssl;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public $is_enabled;


    public function __construct() {
        $this->is_enabled = false;
        $this->host = 'localhost';
        $this->port = 8081;
    }

    public function serialize() {
        return json_encode($this);
    }

    public function unserialize(string $data) {
        $tmp_data = json_decode($data, true);
        foreach ($tmp_data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
