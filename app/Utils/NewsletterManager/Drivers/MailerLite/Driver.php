<?php

namespace App\Utils\NewsletterManager\Drivers\MailerLite;

use App\Helpers\SystemMessageHelper;
use App\Utils\NewsletterManager\BaseDriver;
use Exception;
use Illuminate\Support\Facades\Log;
use MailerLiteApi\MailerLite;

class Driver implements BaseDriver
{
    /**
     * @param $subscriber
     * @return bool
     */
    public function addSubscriber($subscriber)
    {
        try {
            $groupsApi = (new MailerLite(config('newsletter.drivers.mailerlite.api_key')))->groups();
            $result = ConnectionFactory::create($groupsApi, $subscriber);
            $this->checkResult($result);
        } catch (\Exception $exception) {
            Log::error("NewsLetter:MailerLite:Send: can'nt be subscribed");
            return false;
        }
    }

    /**
     * @param $result
     * @return mixed
     */
    private function checkResult($result)
    {
        try {
            if (isset($result)) {
                if (isset($result->error))
                    SystemMessageHelper::addErrorMessage("system_messages.newsletter.dataNotEnough");
                elseif (isset($result->sent)) {
                    if ($result->sent == 0)
                        SystemMessageHelper::addSuccessMessage("system_messages.newsletter.subscribe");
                } else
                    SystemMessageHelper::addErrorMessage("system_messages.newsletter.unsubscribe");
            }
        } catch (Exception $e) {
            Log::error('errorLog for newsletter subscribe : ' . $e->getMessage());
        }
    }
}