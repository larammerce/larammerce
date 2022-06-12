<?php

namespace App\Http\Middleware;

use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\RequestService;
use Closure;

class CustomerInitMiddleware
{

    public function handle($request, Closure $next, $guard = null)
    {
        if (!get_customer_user()->is_initiated) {
            SystemMessageService::addWarningMessage('system_messages.user.incomplete_profile');
            return redirect()->guest(route('customer.profile.show-edit-profile'));
        }
        return $next($request);
    }
}
