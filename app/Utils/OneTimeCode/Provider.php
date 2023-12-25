<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/17/18
 * Time: 3:47 PM
 */

namespace App\Utils\OneTimeCode;

use Illuminate\Support\Facades\Redis;

class Provider
{
    /**
     * @var string
     */
    private static $dataStoreKey = "user:one_time_code";

    /**
     * @var string[]
     */
    private static $waitMinutesForKeyGen = [
        "0.5",
        "1",
        "2",
        "5",
        "15",
        "30",
    ];

    /**
     * count of tries a user can do for checking one time code
     *
     * @var string[]
     */
    private static $securityLevels = [
        5,
        3,
        2,
        1
    ];

    /**
     * @param integer $length
     * @param string $characters
     * @return string
     */
    private static function generateString($length, $characters = '12346789abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ')
    {
        $charLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[mt_rand(0, $charLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param integer $numOfTries
     * @return string
     */
    private static function getWaitingMinutes($numOfTries)
    {
        if ($numOfTries >= count(static::$waitMinutesForKeyGen))
            return end(self::$waitMinutesForKeyGen);
        return self::$waitMinutesForKeyGen[$numOfTries];
    }

    /**
     * @param $key
     * @return float
     * @throws NoLastTryExistException
     */
    private static function getLastTryPassedMinutes($key)
    {
        $lastTry = Redis::get(static::$dataStoreKey . ":last_try:{$key}");
        if ($lastTry) {
            return round((strtotime('now') - $lastTry) / 60.0, 2);
        }
        throw new NoLastTryExistException("There is no record for previous tries.");
    }

    /**
     * @param $key
     * @return bool
     */
    private static function isGenerateCodeAllowed($key)
    {
        return self::getRemainingMinutes($key) <= 0;
    }

    /**
     * @param $key
     * @return float|string
     */
    public static function getRemainingMinutes($key)
    {
        try {
            $passedMinutes = self::getLastTryPassedMinutes($key);
            $numOfTries = Redis::get(static::$dataStoreKey . ":tries_count:{$key}");
            $waitMinutes = static::getWaitingMinutes($numOfTries);
            return $waitMinutes - $passedMinutes;
        } catch (NoLastTryExistException $e) {
            return 0.0;
        }
    }

    /**
     * @param string $key
     * @param int $securityLevelIndex
     * @param bool $tokenAsKey
     * @param bool $needsHash
     * @param int $length
     * @param bool $justNumeric
     * @return void
     * @throws GenerateCodeNotPossibleException
     */
    public static function generate($key, $securityLevelIndex, $tokenAsKey = false, $needsHash = false, $length = 4,
                                    $justNumeric = true)
    {
        if (!self::isGenerateCodeAllowed($key))
            throw new GenerateCodeNotPossibleException("You are not allowed to generate code at the moment.");
        $oneTimeCode = '';
        if ($justNumeric)
            $oneTimeCode = static::generateString($length, '123456789');
        else
            $oneTimeCode = static::generateString($length);

        if ($needsHash)
            $oneTimeCode = bcrypt($oneTimeCode);

        if ($tokenAsKey)
            Redis::set(static::$dataStoreKey . ":key:{$oneTimeCode}", $key, 'EX', 60 * 60);

        Redis::set(static::$dataStoreKey . ":token:{$key}", $oneTimeCode, 'EX', 60 * 60);
        Redis::set(static::$dataStoreKey . ":check_try:{$key}", 0, 'EX', 60 * 60);
        Redis::set(static::$dataStoreKey . ":last_try:{$key}", strtotime("now"), "EX", 60 * 60 * 24);
        Redis::set(static::$dataStoreKey . ":security_level:{$key}", self::$securityLevels[$securityLevelIndex], "EX", 60 * 60 * 24);
        Redis::incr(static::$dataStoreKey . ":tries_count:{$key}");
        Redis::expire(static::$dataStoreKey . ":tries_count:{$key}", 60 * 60 * 24);
    }

    public static function clear($key)
    {
        $token = Redis::get(static::$dataStoreKey . ":token:{$key}");
        if ($token)
            Redis::del(static::$dataStoreKey . ":key:{$token}");

        Redis::del(static::$dataStoreKey . ":token:{$key}");
        Redis::del(static::$dataStoreKey . ":check_try:{$key}");
        Redis::del(static::$dataStoreKey . ":last_try:{$key}");
        Redis::del(static::$dataStoreKey . ":security_level:{$key}");
        Redis::del(static::$dataStoreKey . ":tries_count:{$key}");
    }

    /**
     * @param $key
     * @param $oneTimeCode
     * @param bool $needsHash
     * @return bool
     * @throws SecurityLevelException
     */
    public static function check($key, $oneTimeCode, $needsHash = false)
    {
        $expectedCode = Redis::get(static::$dataStoreKey . ":token:{$key}");
        if ($expectedCode) {
            $checkCount = Redis::get(static::$dataStoreKey . ":check_try:{$key}");
            $securityLevel = Redis::get(static::$dataStoreKey . ":security_level:{$key}");

            if ($checkCount >= $securityLevel) {
                throw new SecurityLevelException("Entered security level `{$securityLevel}` is out of range.");
            }

            Redis::set(static::$dataStoreKey . ":check_try:{$key}", $checkCount + 1, 'EX', 60 * 60);

            if ($needsHash)
                $oneTimeCode = bcrypt($oneTimeCode);

            if ($expectedCode == $oneTimeCode)
                return true;
        }
        return false;
    }

    /**
     * @param string $token
     * @return string
     * @throws OneTimeCodeInvalidTokenException
     */

    public static function getKey($token)
    {
        $key = Redis::get(static::$dataStoreKey . ":key:{$token}");
        if ($key)
            return $key;
        throw new OneTimeCodeInvalidTokenException("There is no record with token : `{$token}`");
    }

    /**
     * @param integer $minutes
     * @return array
     */
    public static function formatRemainingTime($minutes)
    {
        return [
            'remaining_minutes' => (int)$minutes,
            'remaining_seconds' => round(($minutes - (int)$minutes) * 60)
        ];
    }

    public static function formatRemainingTimeByKey($key)
    {
        return self::formatRemainingTime(self::getRemainingMinutes($key));
    }

    /**
     * @param $key
     * @return string | null
     */
    public static function getCode($key)
    {
        return Redis::get(static::$dataStoreKey . ":token:" . $key);
    }
}