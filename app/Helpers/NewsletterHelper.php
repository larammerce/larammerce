<?php
namespace App\Helpers;
use App\Jobs\SubscribeNewsletter;

class NewsletterHelper
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