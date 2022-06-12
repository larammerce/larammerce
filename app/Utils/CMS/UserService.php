<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/19/17
 * Time: 3:49 PM
 */

namespace App\Utils\CMS;


use App\Models\User;
use App\Utils\CMS\Enums\UserHome;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Authenticatable;

class UserService
{
    /**
     * @param GenericUser|User|Authenticatable $user
     * @return string
     */
    public static function getHome($user)
    {
        return $user->is_system_user ? UserHome::ADMIN_HOME : UserHome::CUSTOMER_HOME;
    }

    private static $waitMinutesForSendingEmail = [
        "0.5",
        "1",
        "2",
        "5",
        "15",
        "30",
    ];

    public static function getWaitingMinutesForSendingEmail($numOfTries)
    {
        if ($numOfTries >= count(self::$waitMinutesForSendingEmail))
            return end(self::$waitMinutesForSendingEmail);
        return self::$waitMinutesForSendingEmail[$numOfTries];
    }
}