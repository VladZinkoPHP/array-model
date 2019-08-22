<?php

namespace ZingleCom\ArrayModel\Util;

use Illuminate\Support\Str;

/**
 * Class Arr
 */
class Arr
{
    /**
     * @param array $array
     *
     * @return array
     */
    public static function camelCaseKeys(array $array): array
    {
        foreach (array_keys($array) as $key) {
            if (is_array($array[$key])) {
                $array[$key] = self::camelCaseKeys($array[$key]);
            }
            if (is_object($array[$key])) {
                $objArray    = json_decode(json_encode($array[$key]), JSON_OBJECT_AS_ARRAY);
                $array[$key] = self::camelCaseKeys($objArray);
            }
            $tempValue = $array[$key];
            unset($array[$key]);
            $newKey         = Str::camel($key);
            $array[$newKey] = $tempValue;
        }

        return $array;
    }
}
