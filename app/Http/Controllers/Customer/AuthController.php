<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 11/17/17
 * Time: 5:22 PM
 */

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Utils\CMS\CustomerAuth\{AuthType as CustomerAuthType,
    BadAuthTypeException,
    BadLoginException,
    BadValidationCodePassed,
    CustomerActivationException,
    Provider as CustomerAuthProvider,
    VerificationException
};
use App\Utils\CMS\SystemMessageService;
use App\Utils\CMS\UserService;
use App\Utils\OneTimeCode\{GenerateCodeNotPossibleException,
    OneTimeCodeInvalidTokenException,
    Provider as OneTimeCodeProvider,
    SecurityLevelException
};
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * TODO: we should make global check of auth type, not in every single function.
 * TODO: auth types should be checked, type email should not apply in else scope of type equals mobile.
 *
 * Class AuthController
 * @package App\Http\Controllers\Customer
 */
class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('mobile-auth')->only(['showMobileRegister', 'doMobileRegister']);
    }

    /**
     * @param $type
     * @return Factory|View
     */
    public function showAuth($type): Factory|View
    {
        $type = CustomerAuthType::fix($type);
        return h_view("public.auth-{$type}-show");
    }

    /**
     * @rules(g-recaptcha-response="required|captcha", main_phone="required|mobile_number")
     */
    public function doMobileAuth(Request $request): RedirectResponse
    {
        $mainPhone = $request->get('main_phone');


        if (CustomerAuthProvider::isPasswordAuthPossible(CustomerAuthType::MOBILE, $mainPhone))
            return redirect()->route('customer-auth.show-password-auth', [CustomerAuthType::MOBILE, $mainPhone]);

        try {
            CustomerAuthProvider::sendOneTimeCode(CustomerAuthType::MOBILE, $mainPhone);
            SystemMessageService::addSuccessMessage("system_messages.user.mobile_auth_code_sent",
                ["phone_number" => $mainPhone]);

        } catch (GenerateCodeNotPossibleException $e) {
            SystemMessageService::addErrorMessage('system_messages.one_time_code.repeated_request',
                OneTimeCodeProvider::formatRemainingTimeByKey($mainPhone));
        }

        return redirect()->route('customer-auth.show-check', [CustomerAuthType::MOBILE, $mainPhone]);
    }

    /**
     * @rules(g-recaptcha-response="required|captcha", email="required|email")
     */
    public function doEmailAuth(Request $request): RedirectResponse
    {
        $email = $request->get('email');

        if (CustomerAuthProvider::isPasswordAuthPossible(CustomerAuthType::EMAIL, $email))
            return redirect()->route('customer-auth.show-password-auth',
                [CustomerAuthType::EMAIL, email_encode($email)]);
        try {
            CustomerAuthProvider::sendOneTimeCode(CustomerAuthType::EMAIL, $email);
            SystemMessageService::addSuccessMessage("system_messages.user.email_auth_code_sent",
                compact('email'));
        } catch (GenerateCodeNotPossibleException $e) {
            SystemMessageService::addErrorMessage('system_messages.one_time_code.repeated_request',
                OneTimeCodeProvider::formatRemainingTimeByKey($email));
        }
        return redirect()->route('customer-auth.show-check', [CustomerAuthType::EMAIL, email_encode($email)]);
    }

    public function showCheck($type, $value): \Illuminate\Foundation\Application|Factory|View
    {
        $type = CustomerAuthType::fix($type);
        return h_view("public.auth-{$type}-check", compact('value'));
    }

    public function doCheck(Request $request, $type, $value): RedirectResponse
    {
        $type = CustomerAuthType::fix($type);
        $value = email_decode($value);
        $oneTimeCode = $request->get("one_time_code");
        try {
            $customerUser = CustomerAuthProvider::validateByCode($type, $value, $oneTimeCode);
            if ($customerUser != null) {
                auth('web')->login($customerUser->user);
                SystemMessageService::addInfoMessage("system_messages.user.login_message",
                    ["name" => $customerUser->user->full_name]);
                $customer = get_customer_user();
                foreach (get_local_cart() as $cart_row) {
                    try {
                        $customer->cartRows()->create([
                            "product_id" => $cart_row->product_id,
                            "count" => $cart_row->count,
                            "customer_user_id" => $customer->id
                        ]);
                    } catch (QueryException $exception) {
                        continue;
                    }

                }
                return redirect()->intended(UserService::getHome($customerUser->user));
            } else {
                SystemMessageService::addSuccessMessage("system_messages.one_time_code.valid_one_time_code");
                return redirect()->route("customer-auth.show-register", [$type, email_encode($value)]);
            }
        } catch (SecurityLevelException $e) {
            return redirect()->route('customer-auth.show-auth', CustomerAuthType::MOBILE);
        } catch (BadValidationCodePassed $e) {
            SystemMessageService::addErrorMessage("system_messages.one_time_code.invalid_one_time_code");
            return redirect()->route('customer-auth.show-check', [$type, email_encode($value)]);
        } catch (CustomerActivationException $e) {
            SystemMessageService::addErrorMessage("system_messages.user.account_activation_error");
            return redirect()->route('customer-auth.show-auth', $type);
        }
    }

    public function sendAuthConfirm($type, $value): RedirectResponse
    {
        $type = CustomerAuthType::fix($type);
        $value = email_decode($value);
        try {
            CustomerAuthProvider::sendOneTimeCode($type, $value);
            SystemMessageService::addSuccessMessage("system_messages.user.auth_code_sent");
        } catch (GenerateCodeNotPossibleException $e) {
            SystemMessageService::addErrorMessage('system_messages.one_time_code.repeated_request',
                OneTimeCodeProvider::formatRemainingTimeByKey($value));
        } catch (BadAuthTypeException $e) {
            SystemMessageService::addErrorMessage('system_messages.one_time_code.invalid_auth_type');
        }
        return redirect()->route('customer-auth.show-check', [$type, email_encode($value)]);
    }

    public function showPasswordAuth($type, $value): \Illuminate\Foundation\Application|Factory|View
    {
        $type = CustomerAuthType::fix($type);
        return h_view("public.auth-{$type}-password", compact('value'));
    }

    /**
     * @rules(g-recaptcha-response="required|captcha", password='required')
     */
    public function doPasswordAuth(Request $request, $type, $value): RedirectResponse
    {
        $type = CustomerAuthType::fix($type);
        $value = email_decode($value);
        $password = $request->get("password");

        try {
            $customerUser = CustomerAuthProvider::validateByPassword($type, $value, $password);
            if ($customerUser != null) {
                auth('web')->login($customerUser->user);
                SystemMessageService::addInfoMessage("system_messages.user.login_message",
                    ["name" => $customerUser->user->full_name]);
                return redirect()->intended(UserService::getHome($customerUser->user));
            } else {
                SystemMessageService::addErrorMessage("system_messages.user.login_error");
                return redirect()->route('customer-auth.show-auth', [$type, email_encode($value)]);
            }
        } catch (BadLoginException $e) {
            SystemMessageService::addErrorMessage("system_messages.user.wrong_password");
            return redirect()->route('customer-auth.show-password-auth', [$type, email_encode($value)]);
        }
    }

    public function showRegister($type, $value): \Illuminate\Foundation\Application|Factory|View
    {
        $type = CustomerAuthType::fix($type);
        return h_view("public.auth-{$type}-register", compact('value'));
    }


    /**
     * @rules(name="required|user_alphabet_rule|min:2", family="required|user_alphabet_rule|min:2",
     *     email="email|unique:users", main_phone="required|mobile_number|unique:customer_users",
     *     national_code="required|national_code")
     */
    public function doRegister(Request $request, $type, $value): RedirectResponse
    {
        $type = CustomerAuthType::fix($type);
        $value = email_decode($value);
        $data = $request->only(["name", "family", "main_phone", "email", "national_code"]);
        try {
            $customerUser = CustomerAuthProvider::register($type, $value, $data);
            if ($customerUser != null) {
                auth('web')->login($customerUser->user);
                SystemMessageService::addSuccessMessage("system_messages.user.register_done");
                return redirect()->intended(UserService::getHome($customerUser->user));
            } else {
                SystemMessageService::addErrorMessage("system_messages.user.register_failed");
                return redirect()->to("/");
            }
        } catch (VerificationException $e) {
            SystemMessageService::addErrorMessage("system_messages.user.not_verified_info");
            return redirect()->route('customer-auth.show-register', [$type, email_encode($value)])->withInput();
        } catch (CustomerActivationException $e) {
            SystemMessageService::addSuccessMessage("system_messages.user.account_registered");
            SystemMessageService::addErrorMessage("system_messages.user.account_activation_error");
            return redirect()->to("/");
        }
    }

    /**
     * @rules(token="required")
     */
    public function emailConfirmation(): RedirectResponse
    {
        try {
            $key = OneTimeCodeProvider::getKey(request('token'));
            $user = User::find($key);
            $user->is_email_confirmed = true;
            $user->save();

            SystemMessageService::addSuccessMessage("system_messages.user.email_confirmed");
            OneTimeCodeProvider::clear($key);
            return redirect()->route('customer.profile.index');
        } catch (OneTimeCodeInvalidTokenException|Exception $e) {
            SystemMessageService::addErrorMessage('system_messages.user.email_confirm_error');
        }
        return redirect()->route('public.home');
    }
}
