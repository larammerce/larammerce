<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/16/18
 * Time: 8:46 PM
 */

namespace App\Utils\SMSManager;

use App\Utils\SMSManager\Models\BaseSMSConfig;
use App\Utils\SMSManager\Models\TextMessage;

interface BaseDriver
{
    public function getId(): string;

    public function getDefaultConfig(): BaseSMSConfig;

    public function sendSMS(TextMessage $text_message): bool;
}
