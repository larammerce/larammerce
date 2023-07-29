<?php

namespace App\Http\Middleware;

use App\Helpers\SystemMessageHelper;
use Closure;

class CustomerInitMiddleware
{

    public function handle($request, Closure $next, $guard = null)
    {
        if (!get_customer_user()->is_initiated) {
            SystemMessageHelper::addWarningMessage('system_messages.user.incomplete_profile');
            return redirect()->guest(route('customer.profile.show-edit-profile'));
        }
        return $next($request);
    }
}
