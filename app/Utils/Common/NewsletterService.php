<?php
namespace App\Utils\Common;
use App\Jobs\SubscribeNewsletter;

class NewsletterService
{
    /**
     * @param $subscriber
     */
    public static function subscribe($subscriber)
    {
        $job = new SubscribeNewsletter($subscriber);
        dispatch($job);
    }
}