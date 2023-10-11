<?php

namespace App\Helpers;

class StringHelper {
    /**
     * @param string $str
     * @param int $count_of_spaces
     * @return string[]
     */
    public static function generateSpaceCombinations(string $str, int $count_of_spaces): array {
        $spaces = [];
        $lastPos = 0;
        while (($lastPos = strpos($str, ' ', $lastPos)) !== false) {
            $spaces[] = $lastPos;
            $lastPos = $lastPos + 1;
        }

        $combinations = [];
        $combinations[] = $str; // Original string

        // Generate combinations for the specified number of spaces
        for ($i = 1; $i <= min($count_of_spaces, count($spaces)); $i++) {
            foreach (static::combinations($spaces, $i) as $combination) {
                $tempStr = $str;
                foreach ($combination as $spacePos) {
                    $tempStr = substr_replace($tempStr, '', $spacePos, 1);
                    // Adjust the positions of the remaining spaces after removing one
                    foreach ($combination as &$adjust_pos) {
                        if ($adjust_pos > $spacePos) $adjust_pos--;
                    }
                }
                $combinations[] = $tempStr;
            }
        }

        return $combinations;
    }

    /**
     * @param array $arr
     * @param int $combination_size
     * @return array|array[]
     */
    public static function combinations(array $arr, int $combination_size): array {
        if ($combination_size == 0) {
            return [[]];
        }

        if (count($arr) == 0) {
            return [];
        }

        $head = $arr[0];
        $tail = array_slice($arr, 1);

        $new_combinations = [];
        foreach (static::combinations($tail, $combination_size - 1) as $combination) {
            $new_combinations[] = array_merge([$head], $combination);
        }

        return array_merge($new_combinations, static::combinations($tail, $combination_size));
    }
}
