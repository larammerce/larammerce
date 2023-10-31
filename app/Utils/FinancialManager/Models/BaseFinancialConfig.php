<?php

namespace App\Utils\FinancialManager\Models;

use App\Traits\Inputable;
use JsonSerializable;
use Serializable;

abstract class BaseFinancialConfig extends BaseModel implements JsonSerializable, Serializable {
    use Inputable;

    /**
     * @rules(input_rule="number")
     * @data(input_type="number")
     */
    public int $tax_percentage;

    /**
     * @rules(input_rule="number")
     * @data(input_type="number")
     */
    public int $toll_percentage;

    /**
     * @rules(input_rule="string")
     * @data(input_type="text")
     */
    public string $invoice_tax_heading;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public bool $invoice_show_tax_percentage;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public bool $use_per_product_config;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public bool $is_manual_stock;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public bool $check_exit_tab_sms_notification;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public bool $tax_added_to_price;

    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public bool $is_national_code_required;


    /**
     * @rules(input_rule="bool")
     * @data(input_type="checkbox")
     */
    public bool $is_enabled;


    public function __construct() {
        $this->init();
    }

    private function init() {
        $this->is_enabled = false;
        $this->is_manual_stock = false;
        $this->check_exit_tab_sms_notification = true;
        $this->tax_added_to_price = true;
        $this->tax_percentage = 6;
        $this->toll_percentage = 3;
        $this->use_per_product_config = false;
        $this->invoice_tax_heading = "مالیات و عوارض";
        $this->invoice_show_tax_percentage = false;
        $this->is_national_code_required = true;
    }

    public function serialize(): bool|string|null {
        return json_encode($this);
    }

    public function unserialize(string $data): void {
        $tmp_data = json_decode($data, true);
        $this->init();
        foreach ($tmp_data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

}
