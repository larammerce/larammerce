<?php


namespace App\Http\Controllers\Admin;


use App\Models\WebFormMessage;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class WebFormMessageController extends BaseController
{

    /**
     * @role(super_user, cms_manager)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $web_form_messages = WebFormMessage::orderBy('id', 'DESC')->paginate(WebFormMessage::getPaginationCount());
        return view('admin.pages.web-form-message.index', compact('web_form_messages'));
    }

    /**
     * @role(super_user, cms_manager)
     */
    public function show(WebFormMessage $web_form_message): Factory|View|Application
    {
        if ($web_form_message->user_id == null) {
            $web_form_message->user_id = get_user()->id;
            $web_form_message->save();
        }
        return view('admin.pages.web-form-message.show', [
            'web_form_message' => $web_form_message,
            'web_form_message_fields' => unserialize($web_form_message->data)
        ]);
    }


    public function getModel(): ?string
    {
        return WebFormMessage::class;
    }
}
