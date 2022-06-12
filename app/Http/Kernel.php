<?php

namespace App\Http;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AdminRequestMiddleware;
use App\Http\Middleware\CustomerGuestMiddleware;
use App\Http\Middleware\CustomerInitMiddleware;
use App\Http\Middleware\CustomerMiddleware;
use App\Http\Middleware\DeveloperMiddleware;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\GlobalMiddleware;
use App\Http\Middleware\JsonMiddleware;
use App\Http\Middleware\MobileAuthMiddleware;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\RobotTxtLockMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\RuleMiddleware;
use App\Http\Middleware\VerifyCsrfToken;
use App\Utils\CMS\AdminRequestService;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

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
            VerifyCsrfToken::class,
            SubstituteBindings::class
        ],

        'api' => [
            'throttle:1000,1',
            'bindings',
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
        'developer' => DeveloperMiddleware::class,
        'mobile-auth' => MobileAuthMiddleware::class,
        'robot-txt-lock' => RobotTxtLockMiddleware::class,
        'global' => GlobalMiddleware::class
    ];

    /**
     * @param \Illuminate\Http\Request $request
     * @return Response|RedirectResponse
     */
    protected function sendRequestThroughRouter($request)
    {
        AdminRequestService::setInAdminArea($request);
        return parent::sendRequestThroughRouter($request);
    }
}
