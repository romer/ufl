<?php

namespace Ufl;

/**
 * Class ArrayUtil
 * @package Ufl
 */
class ArrayUtil
{
    const DELIMITER = '.';
    const REPLACEMENT = '{__REPLACE__}';
    public static $delimiter = self::DELIMITER;

    /**
     * Add an element to an array using "dot" notation
     *
     * @param array $array
     * @param string $key
     * @param mixed $value
     *
     * @return array
     */
    public static function add(&$array, $key, $value)
    {
        $setArray = self::get($array, $key, array());
        $setArray[] = $value;
        self::set($array, $key, $setArray);
        return $array;
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (self::toKeys($key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return self::value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * @param string $key
     * @return array
     */
    public static function toKeys($key)
    {
        return explode(self::$delimiter, $key);
    }

    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function value($value)
    {
        return is_callable($value) ? call_user_func($value) : $value;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param array $array
     * @param string $key
     * @param mixed $value
     *
     * @return array
     */
    public static function set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = self::toKeys($key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * @param array $array
     * @param string $key
     * @return int
     */
    public static function count($array, $key)
    {
        return count(self::get($array, $key, array()));
    }

    /**
     * Check if an item exists in an array using "dot" notation.
     *
     * @param array $array
     * @param string $key
     * @return bool
     */
    public static function has($array, $key)
    {
        if (empty($array) || is_null($key)) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (self::toKeys($key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return false;
            }
            $array = $array[$segment];
        }

        return true;
    }

    /**
     * Get the first element of an array. Useful for method chaining.
     *
     * @param array $array
     * @return mixed
     */
    public static function head($array)
    {
        return reset($array);
    }

    /**
     * Return the dot key string from number
     *
     * @param string $key
     * @param int $number
     * @return string
     */
    public static function keyValue($key, $number)
    {
        $keys = self::toKeys($key);
        return isset($keys[$number]) ? str_replace(self::REPLACEMENT, self::$delimiter, $keys[$number]) : null;
    }

    /**
     * Convert to key string
     *
     * @param string|array $value
     * @param bool $disableEscape
     * @return string
     */
    public static function toKey($value, $disableEscape = false)
    {
        if ($disableEscape) {
            return implode(self::$delimiter, (array)$value);
        }

        return implode(
            self::$delimiter,
            array_map(
                function ($val) {
                    return str_replace(ArrayUtil::$delimiter, ArrayUtil::REPLACEMENT, (string)$val);
                },
                (array)$value
            )
        );
    }
}
