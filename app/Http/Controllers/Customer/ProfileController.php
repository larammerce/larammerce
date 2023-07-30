<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 11/17/17
 * Time: 6:54 PM
 */

namespace App\Http\Controllers\Customer;


use App\Jobs\SendEmail;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\History;
use App\Utils\Common\RequestService;
use App\Utils\OneTimeCode\{GenerateCodeNotPossibleException, Provider as OneTimeCodeProvider};
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileController extends BaseController
{
    public function index()
    {
        return h_view('public.profile', [
            "user" => get_user(),
            "customer" => get_customer_user()
        ]);
    }

    public function setLegalPerson()
    {
        $customerUser = get_customer_user();
        if ($customerUser->wasLegalPerson()) {
            SystemMessageService::addErrorMessage("system_messages.user.type_change_not_available");
            return redirect()->back();
        }

        $is_legal_person = false;
        if (session()->has("is_legal_person"))
            $is_legal_person = session()->get("is_legal_person");

        session()->put("is_legal_person", !$is_legal_person);
        SystemMessageService::addSuccessMessage("system_messages.user.type_changed");
        return redirect()->back();
    }

    public function showEditProfile()
    {
        return h_view('public.profile-edit', [
            "user" => get_user(),
            "customer" => get_customer_user()
        ]);
    }

    /**
     * @rules(name="required|user_alphabet_rule",
     *     family="required|user_alphabet_rule",
     *     email="nullable|email|unique:users",
     *     national_code="required|national_code",
     *     is_legal_person="boolean",
     *     gender="nullable|in:".\App\Enums\Customer\Gender::stringValues(),
     *     company_name="required_with:is_legal_person",
     *     economical_code="nullable|regex:/[0-9]{8,16}/|min:8|max:16",
     *     national_id="required_with:is_legal_person|regex:/[0-9]{6,11}/|min:6|max:11",
     *     company_phone="required_with:is_legal_person|regex:/[0-9]{11}/|min:11|max:11",
     *     registration_code="required_with:is_legal_person|regex:/[0-9]{4,11}/|min:4|max:11",
     *     state_id="required_with:is_legal_person|exists:states,id",
     *     city_id="required_with:is_legal_person|exists:cities,id",
     *     bank_account_card_number="nullable|min:16|max:16", bank_account_uuid="nullable|min:24|max:24")
     */
    public function update(Request $request): RedirectResponse
    {
        RequestService::setAttr('birthday_str',
            $request->get('birthday_year') . '/' . $request->get('birthday_month') . '/' .
            $request->get('birthday_day')
        );
        RequestService::setAttr('is_initiated', true);

        $user_exceptions = ['main_phone'];
        $customer_exceptions = [];
        $legal_info_exceptions = [];

        if (!customer_can_edit_profile()) {
            $user_exceptions = array_merge($user_exceptions, ['name', 'family', 'email']);
            $customer_exceptions = array_merge($customer_exceptions, ['national_code']);
            $legal_info_exceptions = array_merge($legal_info_exceptions, []);
        }

        $user = get_user();
        $user->fill($request->except($user_exceptions));
        $user->customerUser->fill($request->except($customer_exceptions));
        if (!$user->customerUser->is_legal_person and
            session()->get("is_legal_person") === true)
            $user->customerUser->is_legal_person = true;

        if ($user->updateFinManCustomer()) {
            $user->save();
            $user->customerUser->save();
        } else {
            SystemMessageService::addErrorMessage("system_messages.user.edit.error_occurred");
            return redirect()->route('customer.profile.show-edit-profile')->withInput();
        }

        if (is_string($user->email) and strlen($user->email) > 0 and !$user->is_email_confirmed) {
            $this->sendMailConfirmEmail($user);
        }

        $user->customerUser->load('legalInfo');
        if ($user->customerUser->is_legal_person) {
            $user->customerUser->legalInfo->fill($request->except($legal_info_exceptions));
            if ($user->customerUser->wasLegalPerson())
                $result = $user->updateFinManLegalCustomer();
            else
                $result = $user->saveFinManLegalCustomer();
            if ($result) {
                $user->customerUser->legalInfo->fill(['is_active', true]);
            } else {
                SystemMessageService::addErrorMessage("system_messages.user.edit.error_occurred");
                return redirect()->route('customer.profile.show-edit-profile')->withInput();
            }
            $user->customerUser->legalInfo->save();
        }

        SystemMessageService::addSuccessMessage('system_messages.user.profile_updated');
        return History::redirectBack(redirect()->route('customer.profile.index'));
    }

    public function showChangePassword()
    {
        return h_view('public.password-change');
    }

    /**
     * @rules(new_password="required|min:6|confirmed", new_password_confirmation="required")
     * @param Request $request
     * @return array|string
     * @throws Exception
     */
    public function doChangePassword(Request $request)
    {
        $user = get_user();
        $user->password = bcrypt($request->get("new_password"));
        $user->save();

        SystemMessageService::addSuccessMessage('system_messages.user.password_changed');
        return redirect()->route('customer.profile.index');
    }

    private function sendMailConfirmEmail($user)
    {
        try {
            OneTimeCodeProvider::generate($user->id, 3, true, true);
            $token = OneTimeCodeProvider::getCode($user->id);

            $job = new SendEmail(
                ['user' => $user, 'token' => $token],
                "public.mail-email-confirmation",
                $user->email,
                $user->name,
                trans("email.email_activation")
            );
            $this->dispatch($job);

            SystemMessageService::addInfoMessage("system_messages.user.confirm_email_sent");
        } catch (GenerateCodeNotPossibleException $e) {
            SystemMessageService::addErrorMessage('system_messages.one_time_code.repeated_request',
                OneTimeCodeProvider::formatRemainingTimeByKey($user->id));
        }
    }
}
