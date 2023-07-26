<?php

namespace App\Utils\PaymentManager\Drivers\Pep;

use App\Interfaces\AttachedFileInterface;
use App\Utils\PaymentManager\Models\BasePaymentConfig;

class Config extends BasePaymentConfig implements AttachedFileInterface
{
    const LOGO_PATH = "/primary_data/payment_drivers/".Driver::DRIVER_ID."/logo.png";
    const HOST = 'https://pep.shaparak.ir';
    // Attention: use the const var with base_path() function.
    const PRIVATE_KEY_ADDRESS = '/data/private/pep/';

    /**
     * @rules(input_rule="required_if:is_enabled,true|integer")
     * @data(input_type="number")
     */
    public $mid;

    /**
     * @rules(input_rule="required_if:is_enabled,true|integer")
     * @data(input_type="number")
     */
    public $tid;

    /**
     * @rules(input_rule="required_if:is_enabled,true|integer")
     * @data(input_type="file")
     */
    public $private_key;

    /*
     * File Methods
     */
    public function hasFile(): bool
    {
        return isset($this->private_key);
    }

    public function getFilePath()
    {
        if ($this->hasFile())
            return $this->private_key;
        return null;
    }

    public function setFilePath($input)
    {
        $uploaded_file = $input["private_key"];
        $destination_path = self::PRIVATE_KEY_ADDRESS;
        $uploaded_file->move(base_path($destination_path), "key.xml");
        $this->private_key = $destination_path . '/key.xml';
    }

    public function removeFile()
    {
        $this->private_key = null;
    }
}
