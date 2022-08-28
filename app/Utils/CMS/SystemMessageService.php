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
    public static function addMessage($text, $parameters, $type, $colorCode): void
    {
        $message = new stdClass();
        $message->text = trans($text, $parameters);
        $message->type = $type;
        $message->color_code = $colorCode;

        $messages = request()->session()->has('systemMessages') ?
            request()->session()->get('systemMessages') : [];
        $messages[] = $message;
        request()->session()->put('systemMessages', $messages);
    }

    public static function hasMessages(): bool
    {
        return request()->session()->has('systemMessages');
    }

    public static function getMessages(): array
    {
        return request()->session()->has('systemMessages') ?
            request()->session()->get('systemMessages') : [];
    }

    public static function flushMessages(): void
    {
        if (request()->session()->has('systemMessages'))
            request()->session()->forget('systemMessages');
    }

    public static function addSuccessMessage($text, $parameters = []): void
    {
        self::addMessage($text, $parameters, SystemMessageType::SUCCESS, SystemMessageColor::SUCCESS);
    }

    public static function addWarningMessage($text, $parameters = []): void
    {
        self::addMessage($text, $parameters, SystemMessageType::WARNING, SystemMessageColor::WARNING);
    }

    public static function addErrorMessage($text, $parameters = []): void
    {
        self::addMessage($text, $parameters, SystemMessageType::ERROR, SystemMessageColor::ERROR);
    }

    public static function addInfoMessage($text, $parameters = []): void
    {
        self::addMessage($text, $parameters, SystemMessageType::INFO, SystemMessageColor::INFO);
    }
}
