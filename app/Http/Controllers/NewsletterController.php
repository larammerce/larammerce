<?php
namespace App\Http\Controllers;

use App\Helpers\NewsletterHelper;
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
        NewsletterHelper::subscribe($subscriber);
        return redirect()->back();
    }
}
