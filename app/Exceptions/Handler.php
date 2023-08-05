<?php

namespace App\Exceptions;

use App\Utils\CMS\AdminRequestService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler {
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register() {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    }

    public function render($request, Throwable $e) {
        if ($e instanceof TokenMismatchException) {
            Log::warning("http_request:token_mismatch_exception:ip:" . $request->ip());
            return redirect()->back();
        }
        if ($e instanceof HttpException and !AdminRequestService::isInAdminArea()) {
            $error_code = $e->getStatusCode();
            if ($error_code === 503 and view()->exists('public.unreachable'))
                $template = 'public.unreachable';
            else if (view()->exists('public.error')) {
                $div_by = 100;
                do {
                    if ($error_code > 0) {
                        $template = "public.error-{$error_code}";
                        $error_code = intval($error_code / $div_by) * $div_by;
                        $div_by *= 10;
                    } else {
                        $error_code = -1;
                        $template = "public.error";
                    }
                } while (!view()->exists($template) and $error_code >= 0);
            } else {
                $template = 'errors.no-theme';
            }
            return response(h_view($template, [
                'code' => $e->getStatusCode(),
                'message' => $e->getMessage()
            ])->render(), $e->getStatusCode());
        }
        if ($e instanceof FileException)
            return redirect()->back()->with(['file_exception' => true]);
        return parent::render($request, $e);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response
     */
    protected function unauthenticated($request, AuthenticationException $exception) {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        return redirect()->guest('login');
    }
}
