<?php defined('SYSPATH') or die('No direct script access.');

class Arr extends Kohana_Arr {

    //a function similar to php internal function array_column (available only php >5.5)
    public static function column($array, $column_key, $index_key = null)
    {
        $result = array();
        if ($index_key) {
            foreach ($array as $row) {
                $result[$row[$column_key]] = $row[$column_key];
            }
        } else {
            foreach ($array as $row) {
                $result[] = $row[$column_key];
            }
        }
        return $result;
    }

    public static function extract($array, array $keys, $default = null, $set_default = true)
    {
        $found = array();
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $found[$key] = $array[$key];
            } else {
                if ($set_default) {
                    $found[$key] = $default;
                }
            }
        }

        return $found;
    }

    public static function set()
    {
        $args = func_get_args();
        $found = array();
        $n = count($args);
        for ($i = 1 ; $i < $n ; ++$i) {
            $key = $args[$i];
            if (array_key_exists($key, $args[0])) {
                $found[$key] = $args[0][$key];
            }
        }

        return $found;
    }
}