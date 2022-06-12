<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 11/29/18
 * Time: 2:49 PM
 */

namespace App\Utils\ShipmentService\Drivers;


use App\Utils\ShipmentService\BaseDriver;

class Express extends BaseDriver
{
    public function getTrackingUrl()
    {
        return '';
    }

    public function isTrackable()
    {
        return false;
    }
}