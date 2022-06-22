<?php

namespace App\Utils\Translation\Exception;

class LocalesNotDefinedException extends \Exception
{
    public static function make(): self
    {
        return new static('Please make sure you have run `php artisan vendor:publish --provider="larammerce-translations\App\Utils\Translations\TranslatableServiceProvider"` and that the locales configuration is defined.');
    }
}
