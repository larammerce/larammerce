<?php
namespace App\Http\Controllers;

use App\Utils\Common\NewsletterService;
use Illuminate\Http\Request;

use Throwable;

class NewsletterController extends Controller
{
    /**
     * @param Request $request
     * @return bool
     * @throws Throwable
     * @rules(email="required|email")
     */

    public function save(Request $request)
    {
        $subscriber = [
            'email' => $request->get('email'),
            'fields' => []
        ];
        NewsletterService::subscribe($subscriber);
        return redirect()->back();
    }
}
