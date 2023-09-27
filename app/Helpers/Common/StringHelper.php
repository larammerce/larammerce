<?php

namespace App\Helpers\Common;

use Closure;
use Exception;

class StringHelper {

    public static function replaceStrings(string $str, array $map): string {
        $search = [];
        $replace = [];

        foreach ($map as $from => $to) {
            $search[] = $from;
            $replace[] = $to;
        }

        return str_replace($search, $replace, $str);
    }

    public static function formatCacheKey(string $cache_key): string {
        return self::replaceStrings($cache_key, [
            "{" => "[",
            "}" => "]",
            "(" => "[",
            ")" => "]",
            "\\" => "|",
            "/" => "|",
            "@" => "%",
            ":" => ";",
            "\"" => "'",
            "." => ",",
        ]);
    }

    /**
     * @throws Exception
     */
    public static function getCacheKey($callable, string ...$parameters): string {
        if ($callable instanceof Closure) {
            throw new Exception("Cant generate unique cache key for Closure instances");
        }

        $parameter_str = implode(",", $parameters);
        $callable_name = self::getCallableName($callable);
        return StringHelper::formatCacheKey("$callable_name($parameter_str)");
    }

    public static function getCallableName($callable): string {
        if (is_string($callable)) {
            return trim($callable);
        } elseif (is_array($callable)) {
            if (is_object($callable[0])) {
                return sprintf("%s::%s", get_class($callable[0]), trim($callable[1]));
            } else {
                return sprintf("%s::%s", trim($callable[0]), trim($callable[1]));
            }
        } else {
            return 'closure';
        }
    }
}