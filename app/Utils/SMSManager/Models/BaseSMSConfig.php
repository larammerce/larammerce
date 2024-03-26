<?php

namespace App\Utils\SMSManager\Models;

use App\Traits\Inputable;
use JsonSerializable;
use Serializable;

abstract class BaseSMSConfig extends BaseModel implements JsonSerializable, Serializable {
    use Inputable;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public $is_enabled;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public $flash_support;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public $can_send_sms_for_invoice_submit;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public $can_send_sms_for_invoice_paid;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public $can_send_sms_for_invoice_cancel;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public $can_send_sms_for_invoice_sending;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public $can_send_sms_for_invoice_sent;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public $can_send_sms_for_invoice_delivered;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public $can_send_sms_for_invoice_survey;


    public function __construct() {
        $this->is_enabled = false;
        $this->flash_support = false;
        $this->can_send_sms_for_invoice_submit = false;
        $this->can_send_sms_for_invoice_paid = true;
        $this->can_send_sms_for_invoice_cancel = true;
        $this->can_send_sms_for_invoice_sending = true;
        $this->can_send_sms_for_invoice_sent = true;
        $this->can_send_sms_for_invoice_delivered = true;
        $this->can_send_sms_for_invoice_survey = true;
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
