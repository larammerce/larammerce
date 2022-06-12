<?php

namespace App\Utils\ShipmentService;

abstract class BaseDriver
{
    abstract function getTrackingUrl();

    abstract function isTrackable();

    function getDeliveryDate($shipmentData) {
        $data = json_decode($shipmentData);
        return $data->{Field::DELIVERY_DATE};
    }

    function getTrackingCode($shipmentData) {
        if (!$this->isTrackable())
            return "";
        $data = json_decode($shipmentData);
        return $data->{Field::TRACKING_CODE};
    }

    function parseData(array $formFields) {
        $fieldPrefix = "shipment_data_";
        $data = [];
        foreach ($formFields as $key => $value) {
            if (strpos($key, $fieldPrefix) !== false) {
                $fieldName = str_replace($fieldPrefix, "", $key);
                $data[$fieldName] = $value;
            }
        }
        return json_encode($data);
    }
}