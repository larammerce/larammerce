<?php

namespace App\Helpers;

class CacheHelper
{
    public function getCacheKey(callable $function, array $parameters): string {
        $key = is_array($function) ? implode('_', $function) : $function;
        foreach ($parameters as $param) {
            $key .= "_" . serialize($param);
        }
        return md5($key);
    }
}
