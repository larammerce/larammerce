<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 9/10/17
 * Time: 3:03 PM
 */

namespace App\Utils\CMS;

use App\Utils\CMS\Enums\SystemMessageColor;
use App\Utils\CMS\Enums\SystemMessageType;
use stdClass;

/**
 * Class FormService
 * @package App\Utils\CMS
 */
class SystemMessageService
{
    public static function addMessage($text, $parameters, $type, $colorCode)
    {
        $message = new stdClass();
        $message->text = trans($text, $parameters);
        $message->type = $type;
        $message->color_code = $colorCode;

        $messages = request()->session()->has('systemMessages') ?
            request()->session()->get('systemMessages') : [];
        array_push($messages, $message);
        request()->session()->put('systemMessages', $messages);
    }

    public static function hasMessages()
    {
        return request()->session()->has('systemMessages');
    }

    public static function getMessages()
    {
        return request()->session()->has('systemMessages') ?
            request()->session()->get('systemMessages') : [];
    }

    public static function flushMessages()
    {
        if (request()->session()->has('systemMessages'))
            request()->session()->forget('systemMessages');
    }

    public static function addSuccessMessage($text, $parameters = [])
    {
        self::addMessage($text, $parameters, SystemMessageType::SUCCESS, SystemMessageColor::SUCCESS);
    }

    public static function addWarningMessage($text, $parameters = [])
    {
        self::addMessage($text, $parameters, SystemMessageType::WARNING, SystemMessageColor::WARNING);
    }

    public static function addErrorMessage($text, $parameters = [])
    {
        self::addMessage($text, $parameters, SystemMessageType::ERROR, SystemMessageColor::ERROR);
    }

    public static function addInfoMessage($text, $parameters = [])
    {
        self::addMessage($text, $parameters, SystemMessageType::INFO, SystemMessageColor::INFO);
    }
}
