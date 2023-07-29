<?php

namespace App\Http\Middleware;

use App\Helpers\SystemMessageHelper;
use App\Utils\CMS\CustomerAuth\SessionService as CustomerAuthSessionService;
use Closure;
use Illuminate\Http\Request;

class MobileAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $urlSegments = $request->segments();
        $phone_number = end($urlSegments);
        if (CustomerAuthSessionService::hasVal($phone_number))
            return $next($request);

        SystemMessageHelper::addErrorMessage("system_messages.user.no_mobile_auth");
        return redirect()->route('customer-auth.show-auth', 'mobile');
    }
}
