<?php


namespace App\Http\Controllers;


use App\Models\WebForm;
use App\Models\WebFormMessage;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\EmailService;
use App\Utils\Common\FileUploadService;
use App\Utils\Common\MessageFactory;
use App\Utils\Common\RequestService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    const FILE_UPLOAD_PREFIX = 'file-';

    /**
     * @rules(g-recaptcha-response="required|captcha",
     *     identifier="required|exists:web_forms",
     *     dynamic_rules=\App\Models\WebForm::getRules(request('identifier')) )
     * @param Request $request
     * @return JsonResponse
     */
    public function saveMessage(Request $request)
    {
        $webForm = WebForm::where("identifier", request('identifier'))->first();
        if ($webForm != null) {
            $formFields = unserialize($webForm->fields);
            $result = [];
            // Todo : file check can be better than this
            foreach ($formFields as $key => $formField) {
                if ($request->hasFile($formField->getIdentifier()))
                    $result[static::FILE_UPLOAD_PREFIX . $formField->getIdentifier()] = $formField->getValue($request);
                else
                    $result[$formField->getIdentifier()] = $formField->getValue($request);
            }
            WebFormMessage::create([
                'web_form_id' => $webForm->id,
                'data' => serialize($result),
            ]);
            if (RequestService::isRequestAjax())
                return response()->json(MessageFactory::create(['system_messages.web_form_message.sent'], 200));
            SystemMessageService::addSuccessMessage("system_messages.web_form_message.sent");
            if (config('mail-notifications.forms.new_form')) {
                $subject = request('identifier');
                $emailAddress = config('mail-notifications.forms.related_mail');
                $template = "public.mail-receive-contact-form";
                EmailService::send([
                    "adminUrl" => config('app.url') . '/admin/web-form-message',
                    "data" => $result,
                ], $template, $emailAddress, $emailAddress, $subject);
            }
        }
        return redirect()->back();
    }
}
