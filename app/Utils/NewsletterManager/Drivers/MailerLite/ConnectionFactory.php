<?php

namespace App\Utils\NewsletterManager\Drivers\MailerLite;

class ConnectionFactory
{
    /**
     * @param $groupsApi
     * @param $subscriber
     * @return bool
     */
    public static function create($groupsApi, $subscriber)
    {
        return $response = $groupsApi->addSubscriber(config('newsletter.drivers.mailerlite.group_id'), $subscriber);
    }
}