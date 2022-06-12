<?php


namespace App\Utils\CMS\CustomerAuth;


use App\Jobs\SendEmail;
use App\Models\{
    CustomerUser,
    User
};
use App\Utils\Common\SMSService;
use App\Utils\OneTimeCode\{
    GenerateCodeNotPossibleException,
    Provider as OneTimeCodeProvider,
    SecurityLevelException
};
use Illuminate\Support\Facades\Hash;

class Provider
{
    /**
     * @param string $type
     * @param string $value
     * @return bool
     */
    public static function isPasswordAuthPossible($type, $value)
    {
        $customerUser = self::getCustomerUser($type, $value);
        return $customerUser != null and $customerUser->user->hasSetPassword();
    }

    /**
     * @param $type
     * @param $value
     * @throws GenerateCodeNotPossibleException
     * @throws BadAuthTypeException
     */
    public static function sendOneTimeCode($type, $value)
    {

        OneTimeCodeProvider::generate($value, 1);
        $oneTimeCode = OneTimeCodeProvider::getCode($value);
        switch ($type) {
            case AuthType::MOBILE:
                SMSService::send("sms-auth-code", $value,
                    compact("oneTimeCode"));
                break;
            case AuthType::EMAIL:
                dispatch(new SendEmail(
                    compact('oneTimeCode'),
                    "public.mail-auth-code",
                    $value,
                    null,
                    trans("email.auth_code")
                ));
                break;
            default:
                throw new BadAuthTypeException("The passed type `{$type}` is not a valid auth type");
        }
    }

    /**
     * @param $type
     * @param $value
     * @param $code
     * @return CustomerUser|null
     * @throws SecurityLevelException
     * @throws CustomerActivationException
     * @throws BadValidationCodePassed
     */
    public static function validateByCode($type, $value, $code)
    {
        if (OneTimeCodeProvider::check($value, $code)) {
            OneTimeCodeProvider::clear($value);
            $customerUser = self::getCustomerUser($type, $value);
            if ($customerUser != null) {
                if (!$customerUser->is_active) {
                    if ($customerUser->user->saveFinManCustomer()) {
                        $customerUser->update(["is_active" => true]);
                    } else {
                        throw new CustomerActivationException("Customer with id: `{$customerUser->user->id}`" .
                            " can not be activated.");
                    }
                }
                return $customerUser;
            } else {
                SessionService::setVal($value);
                return null;
            }
        } else {
            throw new BadValidationCodePassed("The code `{$code}` is not valid for `{$value}`");
        }
    }

    /**
     * @param $type
     * @param $value
     * @param $password
     * @return CustomerUser|null
     * @throws BadLoginException
     */
    public static function validateByPassword($type, $value, $password)
    {
        $customerUser = self::getCustomerUser($type, $value);
        if ($customerUser != null) {
            if (Hash::check($password, $customerUser->user->password)) {
                return $customerUser;
            } else {
                throw new BadLoginException("The entered password is not correct for customer");
            }

        } else {
            SessionService::setVal($value);
            return null;
        }
    }

    /**
     * @param $type
     * @param $value
     * @param $data
     * @return CustomerUser
     * @throws CustomerActivationException
     * @throws VerificationException
     */
    public static function register($type, $value, $data)
    {
        if (!SessionService::hasVal($value)) {
            throw new VerificationException("The request with value `{$value}` is not verified yet.");
        }

        $newUser = User::create([
            'name' => $data["name"],
            'family' => $data["family"],
            'username' => $data["main_phone"],
            'email' => $data["email"],
            'is_email_confirmed' => ($type == AuthType::EMAIL),
            'is_system_user' => false,
            'is_customer_user' => true,
            'gender' => 2,
        ]);

        $customerUser = CustomerUser::create([
            "user_id" => $newUser->id,
            "main_phone" => $data["main_phone"],
            "national_code" => $data["national_code"],
            "is_legal_person" => false,
            "is_active" => false,
            "is_initiated" => true,
        ]);

        if ($newUser->saveFinManCustomer()) {
            $customerUser->update(["is_active" => true]);
            SessionService::forgetVal($value);
            return $customerUser;
        } else {
            throw new CustomerActivationException("The customer with id `{$customerUser->id}` can not be" .
                " activated at this moment.");
        }
    }

    /**
     * @param $type
     * @param $value
     * @return CustomerUser|null
     */
    private static function getCustomerUser($type, $value)
    {
        $type = AuthType::fix($type);
        $customerUser = null;
        if ($type == AuthType::MOBILE)
            $customerUser = CustomerUser::where('main_phone', $value)->first();
        else if ($type == "email")
            $customerUser = CustomerUser::whereHas('user', function ($q) use ($value) {
                $q->where("email", $value);
            })->first();
        return $customerUser;
    }
}
