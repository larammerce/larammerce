<?php


namespace App\Utils\CMS\CustomerAuth;


use Illuminate\Support\Facades\Redis;

class SessionService
{
    private static $key = "auth_info";

    /**
     * @param $value
     * @return void
     */
    public static function setVal($value)
    {
        if (request()->hasSession())
            request()->session()->put(self::$key, $value);
        else
            Redis::set(static::$key . ":token:{$value}", "OK", 'EX', 60 * 60);
    }

    /**
     * @param $value
     * @return void
     */
    public static function forgetVal($value)
    {
        if (request()->hasSession())
            request()->session()->forget(self::$key);
        else
            Redis::del(static::$key . ":token:{$value}");
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function hasVal($value)
    {
        if (request()->hasSession())
            return request()->session()->get(self::$key) == $value;
        else
            return Redis::get(static::$key . ":token:{$value}") == "OK";
    }
}
