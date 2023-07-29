<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/19/17
 * Time: 3:49 PM
 */

namespace App\Services\User;


use App\Models\User;
use App\Utils\CMS\Enums\UserHome;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Authenticatable;

class UserService {
    public static function getHome(User|GenericUser|Authenticatable $user): string {
        return $user->is_system_user ? UserHome::ADMIN_HOME : UserHome::CUSTOMER_HOME;
    }
}