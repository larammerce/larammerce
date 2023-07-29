<?php

namespace App\Helpers;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class HistoryHelper
{
    private static string $STACK_NAME = "history_stack";

    public static function visit(Request $request, Closure $next)
    {

        $referer = $request->header("referer");

        if ($referer === null or !Str::contains($referer, "admin"))
            static::init();

        if ($request->getRealMethod() === "GET") {
            $current_uri = $request->getUri();

            if ($current_uri === static::getLatest(2)) {
                static::pop();
            } else if ($current_uri !== static::getLatest()) {
                static::push($current_uri);
            }

        }

        return $next($request);
    }

    public static function init()
    {
        static::flush();
    }

    public static function back(): ?string
    {
        return static::getLatest(2);
    }

    public static function redirectBack(RedirectResponse $default_response = null): RedirectResponse
    {
        $back = static::back();
        if (request()->has("exit") and $back !== null) {
            return redirect()->to($back);
        }
        return $default_response !== null ? $default_response : redirect()->back();
    }

    private static function push(string $uri)
    {
        Redis::rPush(static::getKey(), $uri);
    }

    private static function pop()
    {
        return Redis::rPop(static::getKey());
    }

    private static function getLatest(int $index = 1)
    {
        $result = Redis::lRange(static::getKey(), -1 * $index, -1 * $index);
        if (count($result) !== 0)
            return $result[0];
        return null;
    }

    private static function flush()
    {
        Redis::del(static::getKey());
    }

    private static function getKey(): string|int
    {
        return static::$STACK_NAME . "_" . session()->getId();
    }
}
