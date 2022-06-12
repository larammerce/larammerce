<?php

namespace App\Http\Middleware;

use App\Utils\PaymentManager\Exceptions\PaymentInvalidDriverException;
use App\Utils\PaymentManager\Provider as PaymentManagerProvider;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    public function __construct(Application $app, Encrypter $encrypter)
    {
        try {
            foreach (PaymentManagerProvider::getEnabledDrivers(true) as $driver) {
                $this->except[] = $driver->getCallbackUri();
            }
        } catch (PaymentInvalidDriverException $e) {
            Log::error("Middleware:VerifyCSRFToken:_construct:{$e->getMessage()}");
        }
        parent::__construct($app, $encrypter);
    }
}
