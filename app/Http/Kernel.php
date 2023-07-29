<?php

namespace App\Http;

use App\Features\Language\LanguageConfig;
use App\Helpers\AdminRequestHelper;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AdminRequestMiddleware;
use App\Http\Middleware\CustomerGuestMiddleware;
use App\Http\Middleware\CustomerInitMiddleware;
use App\Http\Middleware\CustomerMiddleware;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\GlobalMiddleware;
use App\Http\Middleware\JsonMiddleware;
use App\Http\Middleware\MobileAuthMiddleware;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\RobotTxtLockMiddleware;
use App\Http\Middleware\RuleMiddleware;
use App\Http\Middleware\Translate;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Arr;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Symfony\Component\HttpFoundation\Response;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            Translate::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ],

        'api' => [
            'throttle:1000,1',
            'bindings',
            Translate::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'admin' => AdminMiddleware::class,
        'customer' => CustomerMiddleware::class,
        'customer-guest' => CustomerGuestMiddleware::class,
        'customer-init' => CustomerInitMiddleware::class,
        'rule' => RuleMiddleware::class,
        'admin-request' => AdminRequestMiddleware::class,
        'json' => JsonMiddleware::class,
        'permission-system' => PermissionMiddleware::class,
        'mobile-auth' => MobileAuthMiddleware::class,
        'robot-txt-lock' => RobotTxtLockMiddleware::class,
        'global' => GlobalMiddleware::class
    ];

    protected function sendRequestThroughRouter($request): Response
    {
        $new_request = $request;
        AdminRequestHelper::setInAdminArea($new_request);
        $enabled_locales = LanguageConfig::getEnabledLocalesFromEnvFile();
        if ($request->getMethod() == "GET" and !AdminRequestHelper::IsInAdminArea($request) and count($enabled_locales) > 1) {
            $request_segments = $request->segments();
            $locale = Arr::pull($request_segments, 0, "");
            if (in_array($locale, $enabled_locales)) {
                $new_request = $request->duplicate();
                $new_request->merge(["locale" => $locale]);
                $new_request->server->set("REQUEST_URI", implode("/", $request_segments));
            } else {
                $new_request->merge(["locale_fallback" => true]);
            }
        }
        return parent::sendRequestThroughRouter($new_request);
    }
}
