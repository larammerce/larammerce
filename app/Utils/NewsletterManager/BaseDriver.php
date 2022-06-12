<?php

namespace App\Utils\NewsletterManager;


interface BaseDriver
{
    /**
     * @param $subscriber
     * @return boolean
     */
    public function addSubscriber($subscriber);
}