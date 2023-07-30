<?php

namespace App\Helpers;

class CacheHelper {
    public static function getCacheKey(string|array $callable, array $parameters): string {
        $key = is_array($callable) ? implode('_', $callable) : $callable;
        foreach ($parameters as $param) {
            $key .= "_" . serialize($param);
        }
        return md5($key);
    }
}