<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 11/29/18
 * Time: 2:49 PM
 */

namespace App\Utils\ShipmentService\Drivers;


use App\Utils\ShipmentService\BaseDriver;

class Post extends BaseDriver
{
    public function getTrackingUrl()
    {
        return config("delivery.ir_post.tracking_url");
    }

    public function isTrackable()
    {
        return true;
    }
}